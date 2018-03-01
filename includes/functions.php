<?php 
function json($app, $data = [], $status = 200) {
	if(!$app) { return; }
	$app->response()->status($status);
    $app->response()->header('Content-Type', JSON_TYPE);
	$app->response()->body(json_encode($data, JSON_OPTIONS));
}
function plain_r($app, $data = []) {
	if(!$app) { return; }
	$app->response()->status(200);
    $app->response()->header('Content-Type', 'text/plain; charset=utf-8');
	$app->response()->body(print_r($data, true));
}