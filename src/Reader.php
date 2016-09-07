<?php

class Reader extends \Worker {

	public $connection;

	public function __construct($connection){
		$this->connection = $connection;
	}

	public function run(){
		$stdin = fopen("php://stdin", "r");
		$f = fgets($stdin);
		if(!empty($f)) $this->connection->sendData($f);
		$this->run();
	}
}