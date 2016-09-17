<?php

class Manager {

	public static $bots = [];
	public static $config = [];

	public function __construct(array $config){
		self::$config = $config;
	}

	public function init($logger){
		foreach(self::$config as $server => $settings){
			$bot = new Bot($server, $settings, $logger);
			self::$bots[$server] = $bot;
			$bot->init();
		}
	}
}