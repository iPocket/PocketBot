<?php
namespace plugins;

class Test extends \Plugin\PluginBase {

	private $name = "Test Plugin";
	private $version = 1.0;

	public function onEnable(){
		$this->getBot()->getLogger()->log("Test Plugin has been enabled!", "INFO", "Main");
		$this->addCommand(new TestCommand());
		//$this->addListener(new TestListener());
	}
}

class TestCommand extends \Command\Command {

	protected $name = "Test";
	protected $level = 0;
	protected $amount = -1;
	protected $help = "Test";
	protected $usage = "test";

	public function exec(){
		$this->say("It works!");
	}
}

class TestListener extends \Listener\Listener {

	protected $name = "Test Listener";
	protected $keywords = "JOIN";

	public function exec(){
		$this->getBot()->getConnection()->sendData("PRIVMSG " . $this->getArgs()[2] . " :Welcome " . $this->getNick($this->getArgs()[0]));
	}
}