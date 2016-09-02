<?php
namespace Command;

class Rainbow extends \Library\Command\Base {

	public $name = "Rainbow";
    protected $help = 'Rainbow!';
    protected $usage = 'rainbow <msg>';
    protected $amount = -1;
    public $level = 0;
	
    public function exec() {
    	$this->say($this->bot->rainbow(implode(" ", $this->args)));
    }
}
