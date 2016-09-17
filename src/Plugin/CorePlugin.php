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
		$this->addCommand(new EvalCommand());
		$this->addCommand(new PingCommand());
		$this->addCommand(new RawCommand());
		$this->addCommand(new SayCommand());
		$this->addCommand(new JoinCommand());
		$this->addCommand(new PartCommand());
		$this->addCommand(new QuitCommand());
		$this->addCommand(new RestartCommand());
		$this->addCommand(new TimeoutCommand());
		$this->addCommand(new StatsCommand());
		$this->addCommand(new PermsCommand());
	}
}

class HelpCommand extends \Command\Command {

	protected $name = "Help";
	protected $level = 0;
	protected $amount = array(0, 1);
	protected $help = "Shows information about commands";
	protected $usage = "Help [Command]";
	public $aliases = ['?'];
	protected $secret = false;

	public function exec(){
		$args = $this->getArgs();
		$func = $this->getSource()[0] == '#' ? "notice" : "say";
		$command = (!empty($args[0]) ? ucfirst(strtolower($args[0])) : null);
        $commands = $this->getBot()->getCommands();

        if($command == null){
        	$output = array();
        	$commands = $commands;
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

class EvalCommand extends \Command\Command {

	protected $name = "Eval";
	protected $level = 4;
	protected $amount = -1;
	protected $help = "Evaluates the given code and gives output";
	protected $usage = "Eval <Code>";
	public $aliases = [">"];
	protected $secret = true;

	public function exec(){
		$output = $this->getBot()->evaluate(implode(" ", $this->getArgs()));
		if($output[0] !== false){
			$this->say($this->getNick() . ": " . (!empty($output[1]) ? $output[1] : IRCFormat::$FORMAT_ITALIC . "No output."));
		} else {
			$this->say($this->getNick() . ": There was a syntax error with your code. Error: $output[1]");
		}
	}
}

class PingCommand extends \Command\Command {

	protected $name = "Ping";
	protected $level = 0;
	protected $amount = 0;
	protected $help = "Checks if I am working";
	protected $usage = "Ping";
	protected $secret = true;

	public function exec(){
		$this->say($this->getNick() . ": Pong!");
	}
}

class RawCommand extends \Command\Command {

	protected $name = "Raw";
	protected $level = 4;
	protected $amount = -1;
	protected $help = "Sends data to the server";
	protected $usage = "Raw <Data>";
	protected $secret = false;

	public function exec(){
		$this->getBot()->getConnection()->sendData(implode(" ", $this->getArgs()));
	}
}

class SayCommand extends \Command\Command {

	protected $name = "Say";
	protected $level = 3;
	protected $amount = -1;
	protected $help = "Makes me send something to a channel or a user";
	protected $usage = "Say <Channel/User> <Message>";
	protected $secret = false;

	public function exec(){
		if(count($this->getArgs()) <= 1){
			$this->say($this->getNick() . ": Please specify the channel/user to send the message to and the message.");
			return;
		}

		$this->getBot()->getConnection()->sendData("PRIVMSG {$this->getArgs()[0]} :" . implode(" ", array_slice($this->getArgs(), 1)));
	}
}

class JoinCommand extends \Command\Command {

	protected $name = "Join";
	protected $level = 1;
	protected $amount = 1;
	protected $help = "Makes me join a channel";
	protected $usage = "Join <channel>";
	protected $secret = false;

	public function exec(){
		$this->getBot()->getConnection()->sendData("JOIN {$this->getArgs()[0]}");
	}
}

class PartCommand extends \Command\Command {

	protected $name = "Part";
	protected $level = 1;
	protected $amount = 1;
	protected $help = "Makes me part a channel";
	protected $usage = "Part <channel>";
	protected $secret = false;

	public function exec(){
		$this->getBot()->getConnection()->sendData("PART {$this->getArgs()[0]}");
	}
}

class QuitCommand extends \Command\Command {

	protected $name = "Quit";
	protected $level = 4;
	protected $amount = 0;
	protected $help = "Makes me quit IRC";
	protected $usage = "Quit";
	protected $secret = false;

	public function exec(){
		$this->getBot()->getConnection()->sendData("QUIT :Quit requested by {$this->getNick()}");
		stop();
	}
}

class RestartCommand extends \Command\Command {

	protected $name = "Restart";
	protected $level = 3;
	protected $amount = 0;
	protected $help = "Makes me restart";
	protected $usage = "Restart";
	protected $secret = false;

	public function exec(){
		$this->getBot()->getConnection()->sendData("QUIT :Restart requested by {$this->getNick()}");
		exec(ROOT_DIR . DIRECTORY_SEPARATOR . "start." . (\Utils\Utils::getOS() == "win" ? "bat" : "sh"));
		stop();
	}
}

class TimeoutCommand extends \Command\Command {

	protected $name = "Timeout";
	protected $level = 2;
	protected $amount = 1;
	protected $help = "Makes me timeout for seconds";
	protected $usage = "Timeout <seconds>";
	protected $secret = false;

	public function exec(){
		if(($val = intval($this->getArgs()[0])) == false){
    		$this->say($this->getNick() . ": Please enter a valid number");
    		return;
    	}
        $this->getBot()->getConnection()->sendData("QUIT :Timeout for {$this->getArgs()[0]} second(s) requested by " . $this->getNick());
        $this->getBot()->getConnection()->disconnect();
        sleep($this->getArgs()[0]);
        $this->getBot()->init();
	}
}

class StatsCommand extends \Command\Command {

	protected $name = "Stats";
	protected $level = 0;
	protected $amount = 0;
	protected $help = "Shows stats of me";
	protected $usage = "Stats";
	protected $secret = false;

	public function exec() {
    	global $errors;
    	$time = $this->toTime(microtime(true) - START_TIME);
        $this->say($this->getNick() . ": I've been running since " . date("l jS F \@ h:m:s A", START_TIME) . " and been running for " . $time . " with " . ($errors == 0 ? "no" : $errors) . " error(s) occured.");
    }

    private function toTime($seconds){

    	$weeks = (floor($seconds / (60 * 60) / 24)) / 7;

    	$days = (floor($seconds / (60 * 60) / 24)) % 7;

    	$hours = (floor($seconds / (60 * 60))) % 24;

    	$divisor_for_minutes = $seconds % (60 * 60);
    	$minutes = floor($divisor_for_minutes / 60);

    	$divisor_for_seconds = $divisor_for_minutes % 60;
    	$seconds = ceil($divisor_for_seconds);

    	$result = "";
		if (!empty($weeks) && $days > 0)
    		$result .= $weeks . " week";
    	if ($weeks > 1)
    	    $result .= "s";
    	if (!empty($days) && $days > 0)
    		$result .= $days . " day";
    	if ($days > 1)
    	    $result .= "s";
    	if (!empty($hours) && $hours > 0)
    	    $result .= $hours . " hour";
    	if ($hours > 1)
    	    $result .= "s";
    	if (!empty($minutes) && $minutes > 0)
        	$result .= " " . $minutes . " minute";
    	if ($minutes > 1)
        	$result .= "s";
    	if (!empty($seconds) && $seconds > 0)
        	$result .= " " . $seconds . " second";
    	if ($seconds > 1)
        	$result .= "s";
        
    	return trim($result);
    }
}

class PermsCommand extends \Command\Command {

	protected $name = "Perms";
	protected $level = 4;
	protected $amount = array(1, 2, 3);
	protected $help = "Lists all the permissions";
	protected $usage = "Perms <add/remove/list/get/save> [host] [level]";
	public $aliases = ['Levels', 'Permissions'];
	protected $secret = true;

	public function exec(){
		$args = $this->getArgs();

		switch(strtolower($args[0])){

			case "add":
			case "new":
			case "set":

				if(count($args) < 3){
					$this->say($this->getNick() . ": Please specify the user host and the permission level.");
					return;
				}

				$this->getBot()->addPerm($args[1], intval($args[2]));
				$this->say($this->getNick() . ": Permission for " . $this->getUser($args[1]) . " has been set to $args[2]" . " (" . $this->format(intval($args[2])) . ").");

				break;

			case "remove":
			case "rm":
			case "delete":

				if(count($args) < 2){
					$this->say($this->getNick() . ": Please specify the user host.");
					return;
				}

				if($this->getBot()->getPerm($args[1]) !== null){
					$this->getBot()->removePerm($args[1]);
					$this->say($this->getNick() . ": Permission for " . $this->getUser($args[1]) . " has been set to 0 (User).");
				} else {
					$this->say($this->getNick() . ": Permission does not exist.");
				}

				break;

			case "list":
			case "all":

				$perms = $this->getBot()->getPerms();

				$msg = [];
				foreach($perms as $host => $perm){
					$msg[] = $this->getUser($host) . " ($host) => " . $perm . " (" . $this->format($perm) . ")";
				}


				$this->say($this->getNick() . ": " . implode(" | ", $msg) . ".");
				break;

			case "get":
			case "who":

				if(count($args) < 2){
					$this->say($this->getNick() . ": Please specify the user host.");
					return;
				}

				$perm = $this->getBot()->getPerm($args[1]);
				if($perm === null){
					$this->say($this->getNick() . ": " . $this->getUser($args[1]) . " => 0 (User).");
				} else {
					$this->say($this->getNick() . ": " . $this->getUser($args[1]) . " => " . $perm . " (" . $this->format($perm) . ").");
				}

				break;

			case "save":

				$perms = $this->getBot()->getPerms();
				$settings = $this->getBot()->getSettings();

				$settings["perms"] = $perms;

				file_put_contents(ROOT_DIR . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . CONFIG_NAME . ".json", json_encode($settings, JSON_PRETTY_PRINT));

				$this->say($this->getNick() . ": Saved perms successfully.");
				break;

			default:
				$this->say($this->getNick() . ": Subcommand does not exist.");
				break;
		}
	}
}