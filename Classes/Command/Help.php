<?php
namespace Command;

class Help extends \Library\Command\Base {

    public $name = "Help";
    protected $help = 'Show information about commands.';
    protected $usage = 'help [command]';
    protected $amount = array(0, 1);
    public $level = 0;
    
    public function exec(){
        $command = (!empty($this->args[0]) ? \Library\Utils::getText($this->args[0]) : '');
        $commands = $this->bot->commands;
        
        if (empty($command)){
            $output = array();
            foreach ($commands as $name => $details) $out[$name] = $details;
            ksort($out);
            foreach($out as $d => $o) $output[] = "\003" . $this->getColor($o) . $d . "\017";
            $msg = "\00312\02Commands:\017 " . implode(" \00311||\017 ", $output);
            if($this->source{0} == '#'){
                $this->notice("\00312\02Levels:\017 \0034Owner \00313Co-Owner \0037Adminstrator \0038Moderator \0033User");
                $this->notice($msg);
                $this->notice('Use "Help <command>" to get more information about a specific command.');
                $this->say($this->getNick() . ": I gave you some help in notice.");
            } else {
                $this->say("\00312\02Levels:\017 \0034Owner \00313Co-Owner \0037Adminstrator \0038Moderator \0033User");
                $this->say($msg);
                $this->say('Use "Help <command>" to get more information about a specific command.');
            }
        } else {
            $commands = $this->bot->commands;
            foreach ($commands as $name => $details){
                if (trim(ucfirst(strtolower($command))) == $name){
                    if (empty($details->getHelp())){
                        $this->say(ERROR . 'No help available for command ' . $name);
                        return;
                    }
                    $help = $details->getHelp();
                    if($this->source{0} == '#'){
                        $this->notice('12' . $name . ': ' . $help[0]);
                        $this->notice('13Permission Level: ' . $details->level . " (" . $this->getLevel($details->level) . ")");
                        $this->notice('12Command usage: ' . $this->bot->prefix . $help[1]);
                        $this->say($this->getNick() . ": I sent you some help in notice.");
                        return;
                    }
                    $this->say('12' . $name . ': ' . $help[0]);
                    $this->say('13Permission Level: ' . $details->level . " (" . $this->getLevel($details->level) . ")");
                    $this->say('12Command usage: ' . $this->bot->prefix . $help[1]);
                    return;
                }
            }
            $this->say(ERROR . 'No such command: ' . $command);
        }
    }

    private function notice($msg){
        $this->connection->sendData('NOTICE ' . $this->getNick() . ' :' . $msg);
    }

    private function getColor(\Library\Command\Base $cmd){
        if($cmd->level == 4) return "4";
        if($cmd->level == 3) return "13";
        if($cmd->level == 2) return "7";
        if($cmd->level == 1) return "8";
        if($cmd->level == 0) return "3";
    }
}
?>