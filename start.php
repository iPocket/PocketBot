<?php

spl_autoload_register(function ($class){
	$one = __DIR__ . DIRECTORY_SEPARATOR . $class . ".php";
	$two = __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
	$three = __DIR__ . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
	if(file_exists($one)){
		require $one;
	} elseif(file_exists($two)){
		require $two;
	}elseif(file_exists($three)){
		require $three;
	} else {
		echo "File for class $class Not found, var dumping...";
		var_dump($one);
		var_dump($two);
		var_dump($three);
	}
});

include "src/PocketBot.php";