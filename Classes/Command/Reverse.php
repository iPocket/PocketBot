<?php
namespace Command;

class Reverse extends \Library\Command\Base {

    public $name = "Reverse";
    protected $help = 'Reverses a message';
    protected $usage = 'reverse <msg>';
    protected $amount = -1;
    public $level = 0;
   
    public function exec() {
        $this->say(strrev(implode(" ", $this->args)));
    }
}