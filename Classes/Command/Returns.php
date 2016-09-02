<?php
namespace Command;

class Returns extends \Library\Command\Base {

    public $name = "Return";
    protected $help = 'Return eval';
    protected $usage = 'return <etc>';
    protected $amount = -1;
    public $level = 3;
   
    public function exec() {
        $m = implode(" ", $this->args);
        $argument = str_replace(array("\r", "\n"), '', $m);
        $ev = $this->bot->returns($argument);
        $ev = str_split($ev, 425);
        foreach($ev as $e){
            $this->say($e);
            sleep(1);
        }
    }
}