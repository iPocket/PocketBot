<?php
namespace Plugin;

abstract class PluginBase implements Plugin {

	private $name;
	private $version;
	private $bot = null;

	public function onEnable(){
	}

	public function onDisable(){
	}

	public function getName(){
		return $this->name;
	}

	public function getVersion(){
		return $this->verison;
	}

	public function setBot(\Bot $bot){
		$this->bot = $bot;
	}

	public function getBot(){
		return $this->bot;
	}

	protected function addCommand(\Command\Command $c){
		$c->setPlugin($this);
		$this->getBot()->addCommand($c);
	}

	protected function addListener(\Listener\Listener $l){
		$l->setPlugin($this);
		$this->getBot()->addListener($l);
	}
}