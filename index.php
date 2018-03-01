<?php 
set_time_limit(0);
error_reporting(0);

require 'vendor/autoload.php';
require 'includes/config.php';
require 'includes/Database.class.php';
require 'includes/Fetcher.class.php';
require 'includes/Subscriptions.class.php';
require 'includes/TakeoutImport.class.php';
require 'includes/YouTubeVideos.class.php';
require 'includes/functions.php';

$app = new \Slim\Slim([
	'templates.path' => './templates'
]);

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$db->connect();
$app->db = $db;

$fetcher = new Fetcher();
$app->fetcher = $fetcher;
$youtubevideos = new YouTubeVideos($app->fetcher);
$app->youtubevideos = $youtubevideos;
$subscriptions = new Subscriptions($app->db, $app->youtubevideos);
$app->subscriptions = $subscriptions;
$takeout = new TakeoutImport($app->db, $app->subscriptions);
$app->takeout = $takeout;

$app->get('/', function () use ($app) {
    $app->render('index.php', ['path' => HTTP_PATH]);
});

$app->get('/api/subscriptions/list', function () use ($app) {
	return json($app, ['success' => true, 'subscriptions' => $app->subscriptions->list()]);
});

$app->post('/api/subscriptions/upload', function () use ($app) {
	$file = $_FILES['file']['tmp_name'];
	if(empty($file) || !is_file($file)) {
		return json($app, ['success' => false, 'error' => 'Please select a file to upload.'], 500);
	}
	$subscriptions_from_takeout = $app->takeout->import($file);
	$app->subscriptions->import($subscriptions_from_takeout);
	return json($app, ['success' => true, 'imported' => count($subscriptions_from_takeout), 'subscriptions' => $app->subscriptions->list()]);
});

$app->post('/api/subscriptions/clear', function () use ($app) {
	$app->subscriptions->clear();
	return json($app, ['success' => true]);
});

$app->post('/api/subscriptions/fetch', function () use ($app) {
	$app->subscriptions->fetch();
	return json($app, ['success' => true]);
});

$app->get('/api/subscriptions/feed', function () use ($app) {
	return json($app, ['success' => true, 'feed' => $app->subscriptions->feed()]);
});

$app->run();