<?php
namespace Connection;

use Utils\Terminal;

class Client extends \Threaded {

	private $socket;
	public $bot;
	private $reconnects = 0;

	public function __construct($bot){
		$this->bot = $bot;
	}

	public function connect(){
		if($this->isConnected()){
			$this->disconnect();
		}

		$this->socket = fsockopen(($this->bot->useSSL() ? "ssl://" . $this->bot->getIP() : $this->bot->getIP()), $this->bot->getPort());
		if(!$this->isConnected()){
			$this->reconnects++;
			if($this->reconnects < 30){
				trigger_error('Unable to connect to server, This attempt was ' . $this->reconnects . ', Retrying...');
				sleep(3);
				$this->connect();
				return false;
			}
			trigger_error('Maximium reconnects (30) reached, stopping.');
			stop();
        }
		if(!empty($this->bot->getPassword())) $this->sendData("PASS " . $this->bot->getPassword());
		$this->sendData('USER ' . $this->bot->getNick() . ' Layne-Obserdia.de ' . $this->bot->getNick() . ' :' . $this->bot->getName());
		$this->sendData("NICK " . $this->bot->getNick());
        return true;
	}

	public function disconnect(){
		if($this->isConnected()){
			fclose($this->socket);
			return true;
		}
	}

	public function isConnected(){
		if(is_resource($this->socket)){
			return true;
		} else {
			return false;
		}
	}

	public function getData(){
		if(($output = fgets($this->socket)) == false){
			if(!$this->isConnected()){
				trigger_error("Disconnected.");
				$this->connect();
			}
		}
		//$this->bot->download += strlen($output);
		return trim($output, "\r\n");
	}

	public function sendData($data){
		$args = explode(" ", $data);
		$args2 = $args;
		
		foreach($args2 as $n => $arg){
			$args[$n + 1] = $arg;
		}
		$args[0] = $this->bot->getNick();

		if(isset($args[1])){
			switch(strtoupper($args[1])){
				case "PRIVMSG":
					$source = $args[2];
					if($args[3]{0} == ":") $args[3] = substr($args[3], 1);
					$msg = array_slice($args, 3);
					if ($source == $this->bot->getNick()) $source = $this->getUser($data);

					if(preg_match("\01ACTION(.*)\01", $data, $matches) == false){
						$this->bot->getLogger()->log(Terminal::$COLOR_PURPLE . Terminal::$FORMAT_BOLD . "[$args[2]] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_DARK_AQUA . "<" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . "> " . implode(" ", array_slice($args, 3)), "OUTGOING", $this->bot->getServer());
					} else {
						$this->bot->getLogger()->log(Terminal::$COLOR_PURPLE . Terminal::$FORMAT_BOLD . "[$args[2]] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_DARK_AQUA . "*" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . $matches[1], "OUTGOING", $this->bot->getServer());
					}
					$log = false;
					break;

				case "NOTICE":
					if($args[3]{0} == ":") $args[3] = substr($args[3], 1);
					$this->bot->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "--" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . "--" . Terminal::$COLOR_PURPLE . " [$args[2]] " . Terminal::$COLOR_DARK_AQUA . implode(" ", array_slice($args, 3)), "OUTGOING", $this->bot->getServer());
					$log = false;
					break;

				case "JOIN":
					if(isset($args[3]))
						if($args[3]{0} == ":") $args[3] = substr($args[3], 1);

					//$this->bot->getLogger()->log(Terminal::$COLOR_DARK_AQUA . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has joined " . Terminal::$COLOR_PURPLE . $args[2] . Terminal::$COLOR_WHITE, "OUTGOING", $this->bot->getServer());
					$log = false;
					break;

				case "PART":
					if(isset($args[3]))
						if($args[3]{0} == ":") $args[3] = substr($args[3], 1);

					//$this->bot->getLogger()->log(Terminal::$COLOR_DARK_AQUA . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has left " . Terminal::$COLOR_PURPLE . $args[2] . Terminal::$COLOR_GREEN . (isset($args[3]) ? " (" . implode(" ", array_slice($args, 3)) . ")" : ""), "OUTGOING", $this->bot->getServer());
					$log = false;
					break;

				case "QUIT":
					if(isset($args[2]))
						if($args[2]{0} == ":") $args[2] = substr($args[2], 1);

					$this->bot->getLogger()->log(Terminal::$COLOR_DARK_AQUA . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has quit" . Terminal::$COLOR_GREEN . (isset($args[2]) ? " (" . implode(" ", array_slice($args, 2)) . ")" : ""), "OUTGOING", $this->bot->getServer());
					$log = false;
					break;

				case "MODE":
					$this->bot->getLogger()->log(Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " sets mode: " . Terminal::$COLOR_YELLOW . $args[3] . Terminal::$COLOR_AQUA . (isset($args[4]) ? " on " . Terminal::$COLOR_BLUE . $args[4] . Terminal::$COLOR_AQUA : "") . " in " . Terminal::$COLOR_PURPLE . $args[2], "OUTGOING", $this->bot->getServer());
					$log = false;
					break;

				case "NICK":
					$this->bot->getLogger()->log(Terminal::$COLOR_RED . "You" . Terminal::$COLOR_AQUA . " are now known as ". Terminal::$COLOR_GREEN . $args[2], "OUTGOING", $this->bot->getServer());
					$log = false;
					break;

				case "KICK":
					$this->bot->getLogger()->log(Terminal::$COLOR_PURPLE . "[" . $args[2] . "] " . Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has kicked ". Terminal::$COLOR_GREEN . $args[3] . (isset($args[4]) ? Terminal::$COLOR_DARK_AQUA . " (" . Terminal::$COLOR_AQUA . substr(implode(" ", array_slice($args, 4)), 1) . Terminal::$COLOR_DARK_AQUA . ")" : ''), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "TOPIC":
					$this->bot->getLogger()->log(Terminal::$COLOR_PURPLE . "[" . $args[2] . "] " . Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has changed topic to " . Terminal::$COLOR_DARK_AQUA . "\"" . Terminal::$COLOR_AQUA . substr(implode(" ", array_slice($args, 3)), 1) . Terminal::$COLOR_DARK_AQUA . "\"", "INCOMING", $this->getServer());
					$log = false;
					break;

				case "USER":
				case "PASS":
				case "PONG":
					$log = false;
					break;
				default:
					break;
				}
			}
		if(!isset($log)) $this->bot->getLogger()->log($data, "OUTGOING", $this->bot->getServer());
		if(@fwrite($this->socket, $data . "\r\n") == false){
			if(!$this->isConnected()){
				trigger_error("Disconnected.");
				$this->connect();
			}
		}
		//$this->bot->upload += strlen($data);
		return;
	}

	private function getUser($p){
		return $this->bot->getUser($p);
	}
}