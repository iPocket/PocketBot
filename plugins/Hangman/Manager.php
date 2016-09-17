<?php
namespace Hangman;

class Manager {

	private $games = [];

	public function getGames(){
		return $this->games;
	}

	public function getGame($p){
		return isset($this->games[$p]) ? $this->games[$p] : false;
	}

	public function addGame($p){
		if(isset($this->games[$p])) return false;
		$game = new Game(new Player($p), $this);
		$this->games[$p] = $game;
		return true;
	}

	public function removeGame($p){
		if(isset($this->games[$p])){
			$this->games[$p]->getPlayer()->setPlaying(false);
			unset($this->games[$p]);
			return true;
		}
		return false;
	}
}