<?php
namespace Library;

class Logger {

	private $file;
	private $handler;
	private $len = 14;

	public function __construct(){

		if(!is_dir(ROOT_DIR . "/logs")) mkdir(ROOT_DIR . "/logs");
		$dir = ROOT_DIR . "/logs/";
		$this->file = $dir . DIRECTORY_SEPARATOR . "Log";

		$i = 0;
		do {
			$i++;
		} while(file_exists($this->file . $i . '.log'));
		
		$this->file = $this->file . $i . ".log";
		$this->handler = fopen($this->file, 'w');
	}

	public function log($data, $stats = "INFO"){
		$len = 0;
		if(strlen($stats) < $this->len) $len = $this->len - strlen($stats);
		$stats = strtoupper($stats);
		$msg = Utils::getText("\x1b[38;5;87m[" . date("h:m:s") . "]\x1b[m \x1b[38;5;227m[Main/$stats] " . str_repeat(" ", $len) . "\x1b[38;5;34m=> \x1b[m\x1b[38;5;127m" . Utils::toANSI($data) . "\x1b") . "[m" . PHP_EOL;
		echo $this->format($msg);
		fwrite($this->handler, $msg);
		return;
	}

	private function format($msg){
		//preg_replace("/\x1b\[\>(.*)\<\/m\>/i", '', $msg);
		//var_dump($msg);
		return $msg;
	}
}