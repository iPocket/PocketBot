<?php

use Utils\Terminal;

class Logger extends \Thread {

	private $file;
	private $handler;

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

	public function log($data, $stats = "INFO", $server){
		$stats = strtoupper($stats);
		$msg = Terminal::$COLOR_AQUA . "[" . date("h:m:s") . "] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_GOLD . "[$server/$stats]" . Terminal::$COLOR_DARK_GREEN . ": " . Terminal::$FORMAT_RESET . Terminal::$COLOR_WHITE . $data . Terminal::$FORMAT_RESET . PHP_EOL;
		echo $msg;
		fwrite($this->handler, self::format($msg));
		return;
	}

	public static function format($msg){
		preg_replace("/\x1b\[(.)m/", "", $msg);
		return $msg;
	}
}