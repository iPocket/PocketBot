<?php

ini_set("memory_limit", -1);
ini_set("allow_url_fopen", 1);
ini_set("default_charset", "utf-8");

define("ERROR", "\0034\02Error:\017 ");
define("ROOT_DIR", \getcwd());
define("START_TIME", microtime(true));

if(php_sapi_name() !== "cli"){
	throw new \Exception("You must run PocketBot on CLI");
}

if(!extension_loaded("pthreads")){
	throw new \Exception("You must have the pthreads extension.");
}
$errors = 0;

\Utils\Terminal::init($argv);

$logger = new Logger();

set_time_limit(0);
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger){
		global $errors;
		switch($errno){
			case E_NOTICE:
				$errno = "NOTICE";
				break;
			case E_USER_ERROR:
				$errno = "USER ERROR";
				break;
			case E_WARNING:
				$errno = "WARNING";
				break;
			case E_ERROR:
				$errno = "ERROR";
				break;
			case E_PARSE:
				$errno = "PARSE ERROR";
				break;
			default:
				$errno = "UNKNOWN ERROR";
				break;
		}
		$errors++;
		$logger->log("$errstr in $errfile at line $errline", $errno, \Utils\Terminal::$COLOR_RED . "ERROR" . \Utils\Terminal::$COLOR_GOLD);
	});
cli_set_process_title("PocketBot");

if(!isset($argv[1])){
	throw new \Exception("Config file not provided.");
}

if(($config = json_decode(file_get_contents(ROOT_DIR . "/config/$argv[1].json"), true)) == NULL){
	throw new \Exception("Config file does not exist or has invalid syntax, make sure you did not include the file extension.");
}

if(count($config) == 0){
	throw new \Exception("Config file is empty, no servers included.");
}

$manager = new Manager($config);
$manager->init($logger);