<?php
namespace Command;

class Join extends \Library\Command\Base {

	public $name = "Join";
    protected $help = 'Makes me say something in a channel or to a user.';
    protected $usage = 'say <#channel|username> <msg>';
    protected $amount = 1;
    public $level = 2;
	
    public function exec() {
		$this->connection->sendData("JOIN " . $this->args[0]);
    }
}
