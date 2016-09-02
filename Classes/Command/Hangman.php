<?php
namespace Command;

use Hangman\Manager;
use Hangman\Game;
use Hangman\Player;
use Library\Utils;

class Hangman extends \Library\Command\Base {

	public $name = "Hangman";
    protected $help = 'Hangman main command';
    protected $usage = 'hangman <start/stop/guess/stats>';
    protected $amount = array(1, 2);
    public $level = 0;
    public $manager = null;
	
    public function exec() {
		if($this->manager == null) $this->manager = new Manager();

		$command = Utils::getText($this->args[0]);
		$p = $this->getNick();
		$game = $this->manager->getGame($p);


		switch($command){
			case "start":
			case "play":
			case "go":
			case "new":
				$add = $this->manager->addGame($p);
				if($add == false){
					$this->say(ERROR . $this->getNick() . ": " . "You are already playing a game!");
				} else {
					$game = $this->manager->getGame($p);
					$msg = explode("\n", $game->getStats());
					foreach($msg as $m){
						$this->say($m);
					}
				}
				break;
			case "stop":
			case "end":
			case "forfiet":
			case "surrender":
				if($game == false){
					$this->say(ERROR . "You are not playing any game!");
				} else {
					$word = $game->getWord();
					$this->manager->removeGame($p);
					$this->say($this->getNick() . ": " . "You surrender, poor hangman :(");
					$this->say($this->getNick() . ": " . "The word was: " . ucfirst($word));
				}
				break;

			case "guess":
			case "g":
			case "word":
				if($game == false){
					$this->say($this->getNick() . ": " . ERROR . "You are not playing any game!");
					break;
					return;
				}
				if(isset($this->args[1])){
                        $h = $game->guess(strtolower(Utils::getText($this->args[1])));
                        if($h === true){
                            $this->say($this->getNick() . ": " . "\00303Correct!");
                            $v = $game->getStats();
                            $v = explode("\n", $v);
                            foreach($v as $vv) $this->say($vv);
                        } elseif(!$h){
                            $this->say($this->getNick() . ": " . "\00304No\017, there is no \00312" . $this->args[1]);
                            $v = $game->getStats();
                            $v = explode("\n", $v);
                            foreach($v as $vv) $this->say($vv);
                        } elseif($h == "won"){
                            $this->say($this->getNick() . ": " . "\00303You won\017 the game!");
                        } elseif(stripos($h, "token") !== false){
                        	$this->say($this->getNick() . ": " . "You have already guessed \00312" . implode("", array_slice(str_split($h), 5)));
                        } else {
                            $this->say($this->getNick() . ": " . "\00304You lost!\017 Poor Hangman :(");
                            $this->say($this->getNick() . ": " . "The word was : \00312" . ucfirst($h));
                        }
                    } else {
                        $this->say(ERROR . "Missing argument 2 (word)");
                    }
				break;

			case "stats":
				if($game !== false){
					$v = $game->getStats();
                	$v = explode("\n", $v);
                	foreach($v as $vv) $this->say($vv);
                } else {
                	$this->say($this->getNick() . ": " . ERROR . "You are not playing any game!");
                }
				break;
			default:
				$this->say(ERROR . "Subcommand does not exist.");
			break;
		}
    }
}
