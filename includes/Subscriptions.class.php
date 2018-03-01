<?php
Class Subscriptions {
	private $db;
	private $youtubevideos;
	public function __construct($db, $youtubevideos) {
		$this->db = $db;
		$this->youtubevideos = $youtubevideos;
	}
	public function list() {
		return $this->db->fetchMultiple($this->db->query("SELECT * FROM `subscriptions` ORDER BY `channelName` ASC"));
	}
	public function feed($page = 1) {
		if($page < 1) { $page = 1; }
		$limit = $page - 1; 
		return $this->db->fetchMultiple($this->db->query("SELECT * FROM `videos` ORDER BY `published` DESC LIMIT $limit, 48"));
	}
	public function clear() {
		$this->db->query("TRUNCATE TABLE `subscriptions`");
	}
	public function import($data) {
		foreach($data as $item) {
			$title = $this->db->escapeString($item['title']);
			$url = $item['url'];
			$id = $this->db->escapeString(str_replace('https://www.youtube.com/feeds/videos.xml?channel_id=', '', $url));
			$this->db->query("INSERT INTO `subscriptions` (`channelID`, `channelName`, `lastUpdated`) VALUES ('$id', '$title', 0)");
		}
	}
	public function fetch() {
		$minTimeUpdated = time() - 3600;
		$channels_to_fetch = $this->db->fetchMultiple($this->db->query("SELECT * FROM `subscriptions` WHERE `lastUpdated` < $minTimeUpdated ORDER BY `channelName` ASC"));
		foreach($channels_to_fetch as $channel) {
			$channel_id = $channel['channelID'];
			$channelID_ = $this->db->escapeString($channel['channelID']);
			$time = time();
			$channel_response = $this->youtubevideos->get('channel', $channel_id);
			$videos = $channel_response['videos'];
			foreach($videos as $video) {
				$videoID = $this->db->escapeString($video['id']);
				$title = $this->db->escapeString($video['title']);
				$thumbnail = $this->db->escapeString($video['thumbnail']);
				$channelID = $this->db->escapeString($channel_response['channelID']);
				$channelName = $this->db->escapeString($channel_response['channelName']);
				$published = $this->db->escapeInt($video['published']);
				if($this->db->count($this->db->query("SELECT `id` FROM `videos` WHERE `videoId` = '$videoID' LIMIT 1")) == 0) {
					$this->db->query("INSERT INTO `videos` (`videoID`, `title`, `thumbnail`, `channelID`, `channelName`, `published`) VALUES ('$videoID', '$title', '$thumbnail', '$channelID', '$channelName', '$published')");
				}
			}
			$this->db->query("UPDATE `subscriptions` SET `lastUpdated` = '$time' WHERE `channelID` = '$channelID_' LIMIT 1");
		}
	}
}
