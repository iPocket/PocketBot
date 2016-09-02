<?php
namespace Command;

class Cmd extends \Library\Command\Base {

	public $name = "Cmd";
    protected $help = 'Send a command to the server';
    protected $usage = 'cmd <command> <arguments>';
    protected $amount = -1;
    public $level = 2;
	
    public function exec() {
    	if(count($this->args) <= 0){
    		$this->say($this->usage);
    		return;
    	}
    	$this->connection->sendData(implode(" ", $this->args));
    }
}