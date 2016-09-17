<?php
namespace Hangman;

class Player {

	private $name = '';
	private $isPlaying = false;
	private $game = null;

	public function __construct($name){
		$this->name = $name;
	}

	public function getName(){
		return $this->name;
	}

	public function setPlaying($p){
		$this->isPlaying = (bool) $p;
		return;
	}

	public function getGame(){
		return $this->game;
	}

	public function setGame(Game $game){
		$this->game = $game;
		return;
	}
}