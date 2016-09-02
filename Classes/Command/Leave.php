<?php
namespace Command;

class Leave extends \Library\Command\Base {

	public $name = "Leave";
    protected $help = 'Leave a channel';
    protected $usage = 'leave <channel>';
    protected $amount = 1;
    public $level = 2;
	
    public function exec() {
    	$this->connection->sendData("PART " . $this->args[0]);
    }
}