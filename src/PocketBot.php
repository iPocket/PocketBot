<?php

//ini_set("error_reporting", 1);
ini_set("memory_limit", -1);
ini_set("allow_url_fopen", 1);
ini_set("default_charset", "utf-8");

define("ROOT_DIR", \getcwd());
define("START_TIME", microtime(true));

define("VERSION", "Development");
define("NAME", "PocketBot-dev");


function dies(){
	while(true){

	}
}

if(php_sapi_name() !== "cli"){
	trigger_error("You must run " . NAME . " on CLI");
	dies();
}

if(!extension_loaded("pthreads")){
	trigger_error("You must have the pthreads extension.");
	dies();
}

$errors = 0;

\Utils\Terminal::init();

$logger = new Logger();
$logger->log("Starting " . NAME . " v" . VERSION . "...", "INFO", "Main");

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
		$logger->log("$errstr in $errfile at line $errline", $errno, \Utils\Terminal::$COLOR_RED . "Error" . \Utils\Terminal::$COLOR_GOLD);
	});
cli_set_process_title(NAME);

$logger->log("Loading Bot config file...", "INFO", "Main");

if(!isset($argv[1])){
	trigger_error("Config file not provided.");
	dies();
}

$argv[1] = substr($argv[1], 1);

if(($config = json_decode(file_get_contents(ROOT_DIR . "/config/$argv[1].json"), true)) == null){
	trigger_error("Config file does not exist or has invalid syntax, make sure you did not include the file extension.");
	dies();
}

if(count($config) == 0){
	trigger_error("Config file is empty, no servers included.");
	dies();
}

$manager = new Manager($config);
$manager->init($logger);