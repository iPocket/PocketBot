<?php

class Reader extends \Worker {

	public $connection;
	public $switch = "~";

	public function __construct($connection){
		$this->connection = $connection;
	}

	public function run(){
		$stdin = fopen("php://stdin", "r");
		$f = trim(fgets($stdin), "\r\n");

		if(!empty($f)){
			$args = explode(" ", $f);
			if(strtolower($args[0]) == "//switch"){
				if(isset($args[1])){
					$this->switch = $args[1];
				} else {
					$this->connection->bot->getLogger()->log("You need to specify at least 1 argument for /switch", "INFO", "Reader");
				}
			} elseif($this->switch == "~" || ($f{0} == "/" && $f{1} == "/")){
				$this->connection->sendData(substr($f, 2));
			} elseif($this->switch !== "~"){
				if($args[0] == "/me"){
					$this->connection->sendData("PRIVMSG $this->switch :\01ACTION " . implode(" ", array_slice($args, 1)) . "\01");
				} else {
					$this->connection->sendData("PRIVMSG $this->switch :$f");
				}
			}
		}
		$this->run();
	}
}