<?php
namespace Command;

class Perms extends \Library\Command\Base {

    public $name = "Perms";
    protected $help = 'Shows users with perms';
    protected $usage = 'perms';
    protected $amount = 0;
    public $level = 0;
   
    public function exec() {
        $out = array();
        foreach($this->bot->levels as $user => $l){
        	$out[] = $this->nick($user) . " => $l (" . $this->getLevel($l) . ")";
        }

        $this->say(implode(" ", $out));
    }

    private function nick($data){
    	$data = explode("!", $data);
		return trim($data[0], ":");
    }
}