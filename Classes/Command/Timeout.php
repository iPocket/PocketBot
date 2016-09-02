<?php
namespace Command;

class Timeout extends \Library\Command\Base {

    public $name = "Timeout";
    protected $help = 'Makes me quit for seconds and come back';
    protected $usage = 'timeout';
    protected $amount = 1;
    public $level = 2;
   
    public function exec(){
    	if(intval(\Library\Utils::getText($this->args[0])) == false){
    		$this->say("Please enter a number");
    		return;
    	}
        $arg = \Library\Utils::getText($this->args[0]);
        $this->connection->sendData("QUIT :Timeout for $arg second(s) requested by " . $this->getNick());
        sleep($arg);
        $this->bot->connect();
    }
}