<?php

use Utils\Terminal;

class Autoloader {

    public static function load($class) {
        $filename = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($filename)) {
        	return require $filename;
        }
        throw new \Exception('File: "' . $filename . '" not found.');
    }

    public static function error($errno, $errstr, $errfile, $errline){
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
			default:
				$errno = "UNKNOWN ERROR";
				break;
		}
		$errors++;
		echo Terminal::$COLOR_AQUA . "[" . date("h:m:s") . "] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_GOLD . "[Main/" . Terminal::$COLOR_RED . "ERROR" . Terminal::$COLOR_GOLD . "]" . Terminal::$COLOR_DARK_GREEN . ": " . Terminal::$FORMAT_RESET . Terminal::$COLOR_RED . "$errstr in $errfile at line $errline" . Terminal::$FORMAT_RESET . PHP_EOL;
	}
}