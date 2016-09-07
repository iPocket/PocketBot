<?php
namespace Connection;

class Client extends \Threaded {

	private $socket;
	private $bot;

	public function __construct($bot){
		$this->bot = $bot;
	}

	public function connect(){
		if($this->isConnected()){
			$this->disconnect();
		}

		$this->socket = fsockopen(($this->bot->useSSL() ? "ssl://" . $this->bot->getIP() : $this->bot->getIP()), $this->bot->getPort());
		if(!$this->isConnected()) {
			throw new \Exception( 'Unable to connect to server via fsockopen with server: "' . $this->server . '" and port: "' . $this->port . '".' );
			return false;
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
		$output = fgets($this->socket);
		//$this->bot->download += strlen($output);
		return trim($output, "\r\n");
	}

	public function sendData($data, $log = true){
		if($log) $this->bot->getLogger()->log($data, "OUTGOING", $this->bot->getServer());
		fwrite($this->socket, $data . "\r\n");
		//$this->bot->upload += strlen($data);
		return;
	}
}