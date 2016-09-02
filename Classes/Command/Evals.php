<?php
namespace Command;

class Evals extends \Library\Command\Base {

    public $name = "Eval";
    protected $help = 'Eval';
    protected $usage = 'evals <etc>';
    protected $amount = -1;
    public $level = 4;
   
    public function exec() {
        $m = implode(" ", $this->args);
        $argument = str_replace(array("\r", "\n"), '', $m);
        $this->say($this->bot->evals($argument));
    }
}