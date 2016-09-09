<?php
namespace Plugin;

use Utils\IRCFormat;

class CorePlugin extends PluginBase {

	protected $name = "Core";
	protected $author = "System";
	protected $version = "1.0";

	public function onEnable(){
		$this->log("Adding Core commands...");

		$this->addCommand(new HelpCommand());
	}
}

class HelpCommand extends \Command\Command {

	protected $name = "Help";
	protected $level = 0;
	protected $amount = array(0, 1);
	protected $help = "Shows information about commands";
	protected $usage = "Help [Command]";
	protected $secret = false;

	public function exec(){
		$args = $this->getArgs();
		$func = $this->getSource(){0} == '#' ? "notice" : "say";
		$command = (!empty($args[0]) ? ucfirst(strtolower($args[0])) : null);
        $commands = $this->getBot()->getCommands();

        if($command == null){
        	$output = array();
        	ksort($commands);
        	foreach($commands as $name => $cmd)
        		if($cmd->isSecret() == false) $output[] = $this->getColor($cmd) . $name . IRCFormat::$FORMAT_RESET;
        	$cmds = implode(IRCFormat::$COLOR_BLUE . " | " . IRCFormat::$FORMAT_RESET, $output);
        	$perms = IRCFormat::$COLOR_GREEN . "User " . IRCFormat::$COLOR_YELLOW . "Moderator " . IRCFormat::$COLOR_ORANGE . "Adminstrator " . IRCFormat::$COLOR_PINK . "Co-Owner " . IRCFormat::$COLOR_RED . "Owner";
        	$this->$func(IRCFormat::$COLOR_PINK . IRCFormat::$FORMAT_BOLD . "Permissions: " . IRCFormat::$FORMAT_RESET . $perms);
        	$this->$func(IRCFormat::$COLOR_BLUE . IRCFormat::$FORMAT_BOLD . "Commands: " . IRCFormat::$FORMAT_RESET . $cmds);
        	$this->$func(IRCFormat::$COLOR_AQUA . "Use \"{$this->getBot()->getPrefix()}Help [Command]\" For more information about a specific command.");
        } else {
        	$cmd = $this->getBot()->getCommand($command);

        	if($cmd !== null){
        		$this->$func(IRCFormat::$COLOR_BLUE . IRCFormat::$FORMAT_BOLD . $cmd->getName() . ": " . IRCFormat::$FORMAT_RESET  . IRCFormat::$COLOR_AQUA . $cmd->getHelp());
        		$this->$func(IRCFormat::$COLOR_RED . IRCFormat::$FORMAT_BOLD . "Permission: " . IRCFormat::$FORMAT_RESET . IRCFormat::$COLOR_AQUA . $cmd->getLevel() . " ({$this->format($cmd->getLevel())})");
        		$this->$func(IRCFormat::$COLOR_GREEN . IRCFormat::$FORMAT_BOLD . "Usage: " . IRCFormat::$FORMAT_RESET . IRCFormat::$COLOR_AQUA . $this->getBot()->getPrefix() . $cmd->getUsage());
        	} else {
        		$this->$func(IRCFormat::$COLOR_AQUA . "Command does not exist");
        	}
        }
	}

	private function getColor(\Command\Command $cmd){
		switch($cmd->getLevel()){
			case 0:
				return IRCFormat::$COLOR_GREEN;
				break;

			case 1:
				return IRCFormat::$COLOR_YELLOW;
				break;

			case 2:
				return IRCFormat::$COLOR_ORANGE;
				break;

			case 3:
				return IRCFormat::$COLOR_PINK;
				break;

			case 4:
				return IRCFormat::$COLOR_RED;
				break;

			default:
				return null;
				break;
		}
	}
}