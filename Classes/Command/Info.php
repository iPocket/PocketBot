<?php
namespace Command;

class Info extends \Library\Command\Base {

    public $name = "Info";
    protected $help = 'Shows information about the bot';
    protected $usage = 'info';
    protected $amount = 0;
    public $level = 0;
   
    public function exec() {
        $this->say("I've been coded by PocketKiller using PHP " . PHP_VERSION . " with no libraries used.");
    }
}