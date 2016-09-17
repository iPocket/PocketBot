<?php
namespace Plugin;

abstract class PluginBase implements Plugin {

	protected $name;
	protected $author = "Unknown";
	protected $version;
	private $bot = null;

	public function onEnable(){
		return;
	}

	public function onDisable(){
		return;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getAuthor(){
		return $this->author;
	}

	public function setAuthor($author){
		$this->author = $author;
	}

	public function getVersion(){
		return $this->version;
	}

	public function setVersion($version){
		$this->version = $version;
	}

	public function setBot(\Bot $bot){
		$this->bot = $bot;
	}

	public function getBot(){
		return $this->bot;
	}

	public function log($msg){
		$this->getBot()->getLogger()->log($msg, $this->getName(), "Plugins");
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