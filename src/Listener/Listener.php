<?php
namespace Listener;

abstract class Listener {

	private $plugin;
	protected $keywords = '';
	private $bot;
	private $data = '';
	private $args = [];

	public function getPlugin(){
		return $this->plugin;
	}

	public function setPlugin(\Plugin\Plugin $p){
		$this->plugin = $p;
	}

	public function getBot(){
		return $this->bot;
	}

	public function setBot(\Bot $bot){
		$this->bot = $bot;
	}

	public function getKeywords(){
		return (string) $this->keywords;
	}

	public function getData(){
		return $this->data;
	}

	public function getArgs(){
		return (array) $this->args;
	}

	public function getName(){
		return $this->name;
	}

	public function execute($data, $args){
		$this->data = (string) $data;
		$this->args = (array) $args;

		$this->exec();
	}

	public function exec(){
        trigger_error('You have to overwrite the "exec" method and the "execute". Call the parent "execute" and execute your custom "exec".');
	}

	protected function getNick($host){
		$dat = explode("!", $host);
		return trim($dat[0], ":");
	}
}