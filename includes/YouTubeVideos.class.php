<?php
Class YouTubeVideos {
	private $fetcher;
	public function __construct($fetcher) {
		$this->fetcher = $fetcher;
	}
	public function get($key = 'channel', $value) {
		if(!in_array($key, ['channel'])) {
			throw new Exception('Invalid key');
		}
		if($key == 'channel') { $key_uri = 'channel_id'; }
		$data = $this->fetcher->fetchAsBot('https://www.youtube.com/feeds/videos.xml?' . $key_uri . '=' . $value);
		libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;
        $dom->loadXML($data, LIBXML_NOCDATA);
        $dom->encoding = 'utf-8';
        $xml = simplexml_import_dom($dom);
		$videos = [];
		if(!$xml->entry) {
			return [
				'channelID' => '',
				'channelName' => '',
				'videos' => []
			];
		}	
		foreach($xml->entry as $entry) {
			$videos[] = [
				'id' => explode(':', (string) $entry->id)[2],
				'title' => (string) $entry->title,
				'published' => strtotime((string) $entry->published),
				'thumbnail' => 'https://i.ytimg.com/vi/' . explode(':', (string) $entry->id)[2] . '/mqdefault.jpg',
			];
		}
		$output = [
			'channelID' => explode(':', (string) $xml->id)[2],
			'channelName' => (string) $xml->title,
			'videos' => $videos
		];
		return $output;
	}
}
