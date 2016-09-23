<?php
use Utils\Terminal;

class Reader extends \Worker {

	public $bot;
	public $switch = "~";

	public function __construct($bot){
		$this->bot = $bot;
	}

	public function run(){
		do {
			$handle = fopen("php://stdin", 'r');
			$data = trim(fgets($handle), "\r\n");
			$args = explode(" ", $data);

			if(empty($data)){
				continue;
			}

			if($args[0] === "/switch"){
				$this->switch = $args[1];
			} elseif($this->switch === "~" || (($data[0] === "/") && ($args[0] !== "/me"))){
				$this->bot->getConnection()->sendData($data[0] === "/" ? substr($data, 1) : $data);
			} elseif($this->switch !== "~"){
				if($args[0] !== "/me")
					$this->bot->getConnection()->sendData("PRIVMSG $this->switch :$data");
				else
					$this->bot->getConnection()->sendData("PRIVMSG $this->switch :\01ACTION " . implode(" ", array_slice($args, 1)) . "\01");
			}
		} while(true);
	}
}