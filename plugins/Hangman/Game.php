<?php
namespace Hangman;

class Game {

	private $player = '';
	private $chances = 6;
	private $word = '';
	private $guessed = [];
	private $info = [];

	public function __construct(Player $p, Manager $manager){
		$this->manager = $manager;
		$this->player = $p;
		$words = explode("\n", strtolower(file_get_contents(__DIR__ . "\words.txt")));
		$this->word = trim($words[mt_rand(0, count($words) - 1)], "\r\n");
		$this->info = explode(" ", str_repeat("_ ", strlen($this->word)));
		unset($this->info[count($this->info)-1]);
		$p->setGame($this);
		$p->setPlaying(true);
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getWord(){
		return $this->word;
	}

	public function getChances(){
		return $this->chances;
	}

	public function getStats(){
		$info = implode(" ", $this->info);
		return "\00304[\017\00313{$this->player->getName()}\017\00304]\017 \00303||\017 \00304[\00308$info\00304]\017 \00303||\017 \00304[\003{$this->getChances_()}{$this->getChances()}\00304]\017 \00303\017";
		//TODO Use IRCFormat class
	}

	public function end(){
		return $this->manager->removeGame($this->player->getName());
	}

	public function guess($g){
		$g = strtolower($g);
		if(stripos($this->word, $g) !== false && !(in_array($g, $this->info)) && strlen($g) == 1){
			$b = str_split($this->word);
			$this->info[array_search($g, $b)] = $g;
			if(count(array_keys($b, $g)) > 1){
				$this->info[array_keys($b, $g)[1]] = $g;
			}
			if(!in_array("_", $this->info)){
				$this->end();
				return "won";
			}
			$this->guessed[$g] = $g;
			return true;
		} else {
			if($g == $this->word){
				$this->end();
				return "won";
			} elseif(strlen($g) > 1 && $g !== $this->word){
				if(isset($this->guessed[$g])){
					return "token" . $g;
				}
				$this->guessed[$g] = $g;
				$this->chances--;
				if($this->chances <= 0){
					$r = $this->word;
					$this->end();
					return $r;
				}
				$this->guessed[$g] = $g;
				return false;
			}
			if(isset($this->guessed[$g])){
				return "token" . $g;
			}
			$this->guessed[$g] = $g;
			$this->chances--;
			if($this->chances <= 0){
				$r = $this->word;
				$this->end();
				return $r;
			}
			$this->guessed[$g] = $g;
			return false;
		}
	}

	public function getChances_(){
		if($this->chances >= 4) return "10";
		if($this->chances == 3) return "07";
		if($this->chances <= 2) return "13";
	}
}