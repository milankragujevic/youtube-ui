<?php
Class Database {
	private $username;
	private $password;
	private $server;
	private $database;
	private $mysql;
	
	public function __construct($server, $username, $password, $database) {
		global $cfg;
		$this->username = $username;
		$this->password = $password;
		$this->server = $server;
		$this->database = $database;
	}
	
	public function connect() {
		$this->mysql = mysqli_connect($this->server, $this->username, $this->password, $this->database);
		if(!$this->mysql) {
			throw new \Exception('MySQLi_Connect [' . mysqli_connect_errno() . ']: ' . mysqli_connect_error());
		}
		$result = mysqli_query($this->mysql, "SET names 'utf8mb4'");
		$result2 = mysqli_query($this->mysql, "SET CHARACTER SET utf8mb4");
		if(!$result) {
			throw new \Exception("MySQLi_Query [" . mysqli_errno($this->mysql) . "]: " . mysqli_error($this->mysql));
		}
		if(!$result2) {
			throw new \Exception("MySQLi_Query [" . mysqli_errno($this->mysql) . "]: " . mysqli_error($this->mysql));
		}
	}
	
	public function query($query) {
		if(!$this->mysql) {
			throw new \Exception('You must connect to the database!');
		}
		$result = mysqli_query($this->mysql, $query);
		if(!$result) {
			throw new \Exception("MySQLi_Query [" . mysqli_errno($this->mysql) . "]: " . mysqli_error($this->mysql));
		}
		return $result;
	}
	
	public function escapeString($string) {
		if(!$this->mysql) {
			throw new \Exception('You must connect to the database!');
		}
		return mysqli_real_escape_string($this->mysql, $string);
	}
	
	public function escapeInt($int) {
		if(!$this->mysql) {
			throw new \Exception('You must connect to the database!');
		}
		return (int) $int;
	}
	
	public function insertID() {
		if(!$this->mysql) {
			throw new \Exception('You must connect to the database!');
		}
		return mysqli_insert_id($this->mysql);
	}
	
	public function fetchMultiple($result) {
		if(!$this->mysql) {
			throw new \Exception('You must connect to the database!');
		}
		if(is_string($result)) {
			$result = $this->query($result);
		}
		$data = [];
		while($row = mysqli_fetch_assoc($result)) {
			$data[] = $row;
		}
		return $data;
	}
	
	public function count($result) {
		if(!$this->mysql) {
			throw new \Exception('You must connect to the database!');
		}
		if(is_string($result)) {
			$result = $this->query($result);
		}
		return mysqli_num_rows($result);
	}
	
	public function fetchSingle($result) {
		$results = $this->fetchMultiple($result);
		if(isset($results[0])) {
			return $results[0];
		}
		return array();
	}
}