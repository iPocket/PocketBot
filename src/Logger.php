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
		$msg = Terminal::$COLOR_AQUA . "[" . date("h:m:s") . "] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_GOLD . "[$server/$stats]" . Terminal::$COLOR_DARK_GREEN . ": " . Terminal::$FORMAT_RESET . Terminal::$COLOR_WHITE . $data . Terminal::$FORMAT_RESET . PHP_EOL;
		echo $msg;
		fwrite($this->handler, self::format($msg));
		return;
	}

	public static function format($msg){
		$msg = str_replace(Terminal::$FORMAT_BOLD, "", $msg);
		$msg = str_replace(Terminal::$FORMAT_OBFUSCATED, "", $msg);
		$msg = str_replace(Terminal::$FORMAT_ITALIC, "", $msg);
		$msg = str_replace(Terminal::$FORMAT_UNDERLINE, "", $msg);
		$msg = str_replace(Terminal::$FORMAT_STRIKETHROUGH, "", $msg);

		$msg = str_replace(Terminal::$FORMAT_RESET, "", $msg);

		$msg = str_replace(Terminal::$COLOR_BLACK, "", $msg);
		$msg = str_replace(Terminal::$COLOR_DARK_BLUE, "", $msg);
		$msg = str_replace(Terminal::$COLOR_DARK_GREEN, "", $msg);
		$msg = str_replace(Terminal::$COLOR_DARK_AQUA, "", $msg);
		$msg = str_replace(Terminal::$COLOR_DARK_RED, "", $msg);
		$msg = str_replace(Terminal::$COLOR_PURPLE, "", $msg);
		$msg = str_replace(Terminal::$COLOR_GOLD, "", $msg);
		$msg = str_replace(Terminal::$COLOR_GRAY, "", $msg);
		$msg = str_replace(Terminal::$COLOR_DARK_GRAY, "", $msg);
		$msg = str_replace(Terminal::$COLOR_BLUE, "", $msg);
		$msg = str_replace(Terminal::$COLOR_GREEN, "", $msg);
		$msg = str_replace(Terminal::$COLOR_AQUA, "", $msg);
		$msg = str_replace(Terminal::$COLOR_RED, "", $msg);
		$msg = str_replace(Terminal::$COLOR_LIGHT_PURPLE, "", $msg);
		$msg = str_replace(Terminal::$COLOR_YELLOW, "", $msg);
		$msg = str_replace(Terminal::$COLOR_WHITE, "", $msg);

		return $msg;
	}
}