<?php
namespace Command;

class Listen extends \Library\Command\Base {

    public $name = "Listen";
    protected $help = 'Lets users use the bot on a channel/user, or not.';
    protected $usage = 'listen <channel> <true/false>';
    protected $amount = 2;
    public $level = 4;

    public function exec() {
        $argument1 = \Library\Utils::getText($this->args[0]);
        $argument2 = \Library\Utils::getText($this->args[1]);
        if($argument2 == "true"){
            if(isset($this->bot->allowed[$argument1])){
                $this->say("4Error: Already listening.");
            } elseif($argument1 == "activate"){
                $this->bot->listen = false;
                $this->say("3Activated: Now only specificated channels/nicks can use this bot.");
            } else {
                $this->say("3Done: Now listening to all users ($argument1).");
                $this->bot->setListen($argument1, true);
            }
        } elseif($argument2 == "false"){
            if(isset($this->bot->allowed[$argument1])){
                $this->say("3Done: Now not listening to any user ($argument1).");
                $this->bot->setListen($argument1, false);
            } elseif($argument1 == "activate"){
                $this->bot->listen = true;
                $this->say("3Deactivated: now everyone can use this bot.");
            } else {
                $this->say("4Error: Already not listening.");
            }
        } elseif($argument1 == "list"){
            if($this->bot->listen == false){
                $this->say(implode(", ", $this->bot->allowed));
            } else {
                $this->say("Not activated");
            }
        } else {
            $this->say("4Error: Invalid argument.");
        }
    }
}