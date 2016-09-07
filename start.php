<?php

spl_autoload_register(function ($class){
	$one = __DIR__ . DIRECTORY_SEPARATOR . $class . ".php";
	$two = __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $class . ".php";
	if(file_exists($one)){
		require $one;
	} elseif(file_exists($two)){
		require $two;
	} else {
		echo "Fail";
	}
});

include "src/PocketBot.php";