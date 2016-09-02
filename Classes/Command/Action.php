<?php
namespace Command;

class Action extends \Library\Command\Base {

	public $name = "Action";
    protected $help = 'Makes me do an action for something in a channel or to a user.';
    protected $usage = 'action <#channel|username> <msg>';
    protected $amount = -1;
    public $level = 1;
	
    public function exec() {
		
		if (!strlen($this->args[0]) OR !strlen($this->args[1])){
			$this->say("Usage :" . $this->usage);
			return;
		}
		$this->connection->sendData('PRIVMSG ' . $this->args[0] . " :\01ACTION " . trim(implode( ' ', array_slice( $this->args, 1 ) )) . "\01"); 
    }
}
