<?php
namespace Library;


class Reader extends \Worker{

	public $bot;

	public function __construct($bot){
		$this->bot = $bot;
	}

	public function run(){
		$stdin = fopen("php://stdin", "r");
		$f = fgets($stdin);
		fwrite($this->bot, $f . "\r\n");
		$this->run();
	}
}