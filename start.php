<?php

require 'Classes\Autoloader.php';

gc_enable();
ini_set("memory_limit", -1);
ini_set("allow_url_fopen", 1);
ini_set("default_charset", "utf-8");
mb_internal_encoding("ASCII");
define("ERROR", "\0034Error: ");
define("SUCCESS", "\0033Done: ");
define("RESULT", "\00312Result: ");
define("ROOT_DIR", __DIR__);
define("START_TIME", microtime(true));

spl_autoload_register('Autoloader::load');

set_time_limit(0);
set_error_handler("customError");
cli_set_process_title("PocketBot");
$errors = 0;
if(!isset($argv[1])){
	throw new Exception("Config file not provided.");
	exit(255);
}

function customError($errno, $errstr, $errfile, $errline){
	global $errors;
	/*if($errno == E_NOTICE) $errno = "NOTICE";
	if($errno == E_USER_ERROR) $errno = "USER ERROR";
	if($errno == E_WARNING) $errno = "WARNING";
	if($errno == E_ERROR) $errno = "ERROR";
	if(is_int($errno)) $errno = "UNKNOWN ERROR";*/
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
		default:
			$errno = "UNKNOWN ERROR";
			break;
	}
	$len = 0;
	if(strlen($errno) < 14) $len = 14 - strlen($errno);
	$errors++;
	echo "\x1b[38;5;87m[" . date("h:m:s") . "]\x1b[m \x1b[38;5;227m[Main/\x1b[38;5;124m$errno\x1b[m\x1b[38;5;227m]\x1b[38;5;34m" . str_repeat(" ", $len) . " => \x1b[38;5;124m$errstr in $errfile at line $errline" . PHP_EOL;
}

$config = json_decode(file_get_contents(__DIR__ . "/$argv[1].json"), true);
$bot = new Library\Bot($config);
foreach($config['commands'] as $cmd){
	$reflector = new ReflectionClass($cmd);
    $command = $reflector->newInstanceArgs(array());

	$bot->addCommand($command);
}
$bot->connect();

?>