<?php

use Utils\Terminal;
use Utils\IRCFormat;

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
		$msg = Terminal::$COLOR_AQUA . "[" . date("h:i:s") . "] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_GOLD . "[$server/$stats]" . Terminal::$COLOR_DARK_GREEN . ": " . Terminal::$FORMAT_RESET . Terminal::$COLOR_WHITE . $data . Terminal::$FORMAT_RESET . PHP_EOL;
		echo self::removeFormatCodes($msg);
		fwrite($this->handler, self::format($msg));
		return;
	}

	public static function format($msg){
		return self::removeFormatCodes(self::removeEscapeCodes($msg));
	}

	public static function removeEscapeCodes($msg){
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

	public static function removeFormatCodes($msg){
		$msg = str_replace(IRCFormat::$FORMAT_UNDERLINE, "", $msg);
		$msg = str_replace(IRCFormat::$FORMAT_BOLD, "", $msg);
		$msg = str_replace(IRCFormat::$FORMAT_REVERSE, "", $msg);

		$msg = str_replace(IRCFormat::$FORMAT_RESET, "", $msg);

		$msg = str_replace(IRCFormat::$COLOR_WHITE, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_BLACK, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_BLUE, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_GREEN, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_RED, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_BROWN, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_PURPLE, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_ORANGE, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_YELLOW, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_LIGHT_GREEN, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_DARK_AQUA, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_AQUA, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_LIGHT_BLUE, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_PINK, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_GRAY, "", $msg);
		$msg = str_replace(IRCFormat::$COLOR_LIGHT_GRAY, "", $msg);

		//Another ones, so \0034 can be also escaped.

		$msg = str_replace("\0030", "", $msg);
		$msg = str_replace("\0031", "", $msg);
		$msg = str_replace("\0032", "", $msg);
		$msg = str_replace("\0033", "", $msg);
		$msg = str_replace("\0034", "", $msg);
		$msg = str_replace("\0035", "", $msg);
		$msg = str_replace("\0036", "", $msg);
		$msg = str_replace("\0037", "", $msg);
		$msg = str_replace("\0038", "", $msg);
		$msg = str_replace("\0039", "", $msg);

		return $msg;
	}
}