<?php
namespace Command;

class Say extends \Library\Command\Base {

	public $name = "Say";
    protected $help = 'Makes me say something in a channel or to a user.';
    protected $usage = 'say <#channel|username> <msg>';
    protected $amount = -1;
    public $level = 1;
	
    public function exec() {
		
		if (!strlen($this->args[0]) OR !strlen($this->args[1])){
			$this->say("Usage :" . $this->usage);
			return;
		}
		
		$this->connection->sendData('PRIVMSG ' . $this->args[0] . ' :'. trim(implode( ' ', array_slice( $this->args, 1 ) ))); 
    }
}
