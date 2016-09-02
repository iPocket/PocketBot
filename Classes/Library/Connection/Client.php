<?php
namespace Library\Connection;

class Client {

	public $socket = null;
	private $server = "";
	private $port = 0;
	public $joined = false;
	public $name = "";
	public $nick = "";
	public $password = '';
	public $channels = [];
	private $bot;
	private $ssl = false;
	private $reconnects = 0;

	public function __construct($bot){
		global $config;
		$this->bot = $bot;
		$this->server = $config['server'];
		$this->port = $config['port'];
		$this->name = $config['name'];
		$this->nick = $config['nick'];
		$this->password = $config['password'];
		$this->channels = $config['channels'];
		$this->ssl = $config['ssl'];
	}

	public function __destruct(){
		$this->disconnect();
	}

	public function connect(){
		if(!$this->isConnected()){
			$this->disconnect();
		}
		$this->socket = fsockopen(($this->ssl ? "ssl://" . $this->server : $this->server), $this->port);
		if(!$this->isConnected()) {
			throw new \Exception( 'Unable to connect to server via fsockopen with server: "' . $this->server . '" and port: "' . $this->port . '".' );
			return false;
        }
		if(!empty($this->password)) $this->sendData("PASS " . $this->password);
		$this->sendData('USER ' . $this->nick . ' Layne-Obserdia.de ' . $this->nick . ' :' . $this->name);
		$this->sendData("NICK $this->nick");
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
		if(!$output = @fgets($this->socket, 256)) return false;
		$this->bot->download += strlen($output);
		return trim($output, "\r\n");
	}

	public function sendData($data){
		$out = explode(" ", $data);
		$out[0] = "\x1b[38;5;83m$out[0]\x1b[m";
		$out[1] = "\x1b[38;5;227m$out[1]\x1b[m\x1b[38;5;127m";
		$this->bot->logger->log(\Library\Utils::toANSI(implode(" ", $out)), "OUTGOING");
		if(!@fwrite($this->socket, $data . "\r\n")){
			if($this->reconnects > 25) return false;
			$this->reconnects++;
			$this->connect();
			return false;
		}
		$this->bot->upload += strlen($data);
		return true;
	}
}