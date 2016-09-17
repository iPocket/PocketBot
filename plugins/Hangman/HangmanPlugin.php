<?php
namespace Hangman;

class HangmanPlugin extends \Plugin\PluginBase {

	protected $name = "Hangman";
	protected $author = "PocketKiller";
	protected $version = "1.0";

	public function onEnable(){

		$this->addCommand(new Hangman());
	}
}