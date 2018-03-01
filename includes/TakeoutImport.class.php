<?php
Class TakeoutImport {
	private $db;
	private $subscriptions;
	public function __construct($db, $subscriptions) {
		$this->db = $db;
		$this->subscriptions = $subscriptions;
	}
	public function import($file) {	
		$data = file_get_contents($file);
		libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;
        $dom->loadXML($data, LIBXML_NOCDATA);
        $dom->encoding = 'utf-8';
        $opml = simplexml_import_dom($dom);
		$output = [];
		foreach($opml->body->outline->outline as $item) {
			$output[] = [
				'title' => (string) $item->attributes()->title,
				'url' => (string) $item->attributes()->xmlUrl
			];
		}
		return $output;
	}
}
