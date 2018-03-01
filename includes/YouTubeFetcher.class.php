<?php 
namespace PutCut;
Class YouTubeFetcher {
	private $api_key;
	private $fch;
	private $db;
	public function getVideoID($url) {
		preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
		if(isset($matches[0])) {
			return $matches[0];
		} else {
			return false;
		}
	}
	public function getThumbnailURL($id) {
		return 'https://i.ytimg.com/vi/' . $id . '/sddefault.jpg';
	}
	public function getVideoInfo($id) {
		$api = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $id . '&locale=en-US&key=' . $this->api_key;
		$data = $this->fch->getDataFromURL($api);
		$data = json_decode($data, true);
		$item = array(
			'id' => $data['items'][0]['id'],
			'title' => $data['items'][0]['snippet']['title'],
			'description' => substr($data['items'][0]['snippet']['description'], 0, 255) . '...',
			'channel' => array(
				'title' => $data['items'][0]['snippet']['channelTitle'],
				'id' => $data['items'][0]['snippet']['channelId']
			),
			'thumbnail' => $data['items'][0]['snippet']['thumbnails']['medium']['url'],
			'published' => strtotime($data['items'][0]['snippet']['publishedAt']),
			'duration' => $this->convertDuration($data['items'][0]['contentDetails']['duration'])
		);
		$this->memorizeItem($item);
		return $item;
	}
	public function convertDuration($ytDuration) {
		$di = new \DateInterval($ytDuration);
		$totalSec = 0;
		if($di->h > 0) {
			$totalSec += $di->h*3600;
		}
		if($di->i > 0) {
			$totalSec += $di->i*60;
		}
		$totalSec += $di->s;
		return $totalSec;
	}
	public function memorizeItem($item) {
		$id = $this->db->escapeString($item['id']);
		$title = $this->db->escapeString($item['title']);
		$description = $this->db->escapeString($item['description']);
		$channel_id = $this->db->escapeString($item['channel']['id']);
		$channel_title = $this->db->escapeString($item['channel']['title']);
		$thumbnail = $this->db->escapeString($item['thumbnail']);
		$published = $this->db->escapeString($item['published']);
		$duration = $this->db->escapeInt($item['duration']);
		/*
		item_id	varchar(255)	 
		provider	varchar(255)	 
		title	varchar(255)	 
		description	text	 
		thumbnail	varchar(255)	 
		channel_id	varchar(255)	 
		channel_title	varchar(255)	 
		duration	int(15)	 
		published	int(15)
		*/
		if($this->db->count($this->db->query("SELECT * FROM videos_data WHERE item_id = '$id' AND provider = 'youtube' LIMIT 1")) == 0) {
			$this->db->query("INSERT INTO videos_data (item_id, provider, title, description, thumbnail, channel_id, channel_title, duration, published) VALUES ('$id', 'youtube', '$title', '$description', '$thumbnail', '$channel_id', '$channel_title', '$duration', '$published')");
		}
	}
	public function embedVideo($id, $width) {
		$height = ($width / (16 / 9));
		return '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';
	}
	public function getInfoFromDB($id) {
		$id = $this->db->escapeString($id);
		return $this->db->fetchSingle($this->db->query("SELECT * FROM videos_data WHERE item_id = '$id' AND provider = 'youtube' LIMIT 1"));
	}
	public function __construct() {
		global $cfg;
		global $db;
		$this->api_key = $cfg['api_keys']['youtube'];
		$this->fch = new \PutCut\Fetcher();
		$this->db = $db;
	}
}