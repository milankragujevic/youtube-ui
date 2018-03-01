<?php
Class Fetcher {
	private $headers;
	public function __construct() {
		$headers = ['User-Agent: youtube-ui/1.0 (+https://github.com/milankragujevic/youtube-ui)', 'Accept-Language: en-US;q=0.6,en;q=0.4'];
		stream_context_set_default([
			'http' => [
				'header' => implode("\r\n", $headers)
			]
		]);
		$this->headers = $headers;
	}
	public function fetchContentLengthAsBot($url) {
		$headers = get_headers($url, 1);
		$length = 0;
		if(isset($headers['Content-Length'])) {
			$length = $headers['Content-Length'];
		}
		if(isset($headers['content-length'])) {
			$length = $headers['content-length'];
		}
		return $length; 
	}
	public function fetchAsBot($url) {
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_ENCODING , 'gzip');
        $result = curl_exec($ch);
        curl_close($ch);
		return $result;
	}
}
