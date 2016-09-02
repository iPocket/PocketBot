<?php
namespace Command;

class Quit extends \Library\Command\Base {

    public $name = "Quit";
    protected $help = 'Makes me quit IRC';
    protected $usage = 'quit';
    protected $amount = 0;
    public $level = 4;
   
    public function exec() {
        $this->connection->sendData("QUIT :Quit requested by " . $this->getNick());
        exit;
    }
}