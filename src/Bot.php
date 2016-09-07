<?php

use Connection\Client;
use Utils\Terminal;

class Bot {

	private $settings = [];

	private $connection = null;
	private $logger = null;

	private $plugins = [];
	private $commands = [];
	private $listeners = [];

	private $joined = false;

	private $server = '';
	private $ip = '';
	private $port = 0;
	private $nick = '';
	private $name = '';
	private $password = '';
	private $prefix = '';
	private $channels = [];
	private $levels = [];
	private $ssl = false;


	public function __construct($name, $settings, Logger $logger){
		$this->settings = (array) $settings;
		$this->server = (string) $name;
		$this->ip = (string) $settings['ip'];
		$this->port = (int) $settings['port'];
		$this->nick = (string) $settings['nick'];
		$this->name = (string) $settings['name'];
		$this->password = (string) $settings['password'];
		$this->prefix = (string) $settings['prefix'];
		$this->ssl = (bool) $settings['ssl'];
		$this->channels = (array) $settings['channels'];
		$this->levels = (array) $settings['levels'];
		$this->logger = $logger;
	}

	public function init(){
		$this->connection = new Client($this);
		foreach(glob(ROOT_DIR . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "*.php") as $p){
			$plugin = "plugins\\" . basename($p, ".php");
			$p = new $plugin();
			if($p instanceof \Plugin\Plugin)
			$this->addPlugin($p);
		}
		$this->getConnection()->connect();
		$this->main();
	}

	public function getNick(){
		return $this->nick;
	}

	public function setNick($nick){
		$this->nick = (string) $nick;
		$this->connection->sendData("NICK $nick");
	}

	public function getName(){
		return $this->name;
	}

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = (string) $password;
		$this->connection->sendData("PASS $password");
	}

	public function getServer(){
		return $this->server;
	}

	public function getIP(){
		return $this->ip;
	}

	public function getPort(){
		return $this->port;
	}

	public function getPlugins(){
		return $this->plugins;
	}

	public function getPlugin($p){
		return isset($this->plugins[$p]) ? $this->plugins[$p] : null;
	}

	public function addPlugin(Plugin\Plugin $p){
		$p->setBot($this);
		$p->onEnable();
		$this->plugins[$p->getName()] = $p;
	}

	public function removePlugin(Plugin\Plugin $p){
		$p->onDisable();
		unset($this->plugins[$p->getName()]);
	}

	public function getCommands(){
		return $this->commands;
	}

	public function getCommand($c){
		return isset($this->commands[$c]) ? $this->commands[$c] : null;
	}

	public function addCommand(Command\Command $c){
		$c->setBot($this);
		$this->commands[$c->getName()] = $c;
	}

	public function removeCommand(Command\Command $c){
		unset($this->commands[$c->getName()]);
	}

	public function executeCommand($data, $args, $source, $name){
        $command = $this->getCommand($name);
        if($command == null) return;
        $command->execute($data, $args, $source);
	}

	public function getListeners(){
		return $this->listeners;
	}

	public function getListener($l){
		return isset($this->listeners[$l]) ? $this->listeners[$l] : null;
	}

	public function addListener(Listener\Listener $l){
		$l->setBot($this);
		$this->listeners[$l->getName()] = $l;
	}

	public function removeListener(Listener\Listener $l){
		unset($this->listeners[$l->getName()]);
	}

	public function executeListener($data, $args, $name){
		$listener = $this->getListener($name);
		if($listener == null) return;
		$listener->execute($data, $args);
	}

	public function getConnection(){
		return $this->connection;
	}

	public function getLogger(){
		return $this->logger;
	}

	public function getPrefix(){
		return $this->prefix;
	}

	public function setPrefix($prefix){
		$this->prefix = $prefix;
	}

	public function getChannels(){
		return $this->channels;
	}

	public function getLevels(){
		return $this->levels;
	}

	public function getLevel($host){
		return isset($this->levels[(string) $host]) ? $this->levels[(string) $host] : null;
	}

	public function addLevel($host, $level){
		$this->levels[(string) $host] = (int) $level;
	}

	public function removeLevel($host){
		unset($this->levels[(string) $host]);
	}

	public function hasLevel($l){
		return isset($this->levels[$l]);
	}

	public function useSSL(){
		return $this->ssl;
	}

	public function getUser($data){
		$dat = explode("!", $data);
		return trim($dat[0], ":");
	}

	public function evaluate($p){
		eval($p);
	}

	private function main(){
		$data = $this->getConnection()->getData();
		$args = explode(" ", $data);

		if(empty($data)){
			$this->main();
			return;
		}

		if($data{0} == ":") $data = substr($data, 1);

		//$this->getLogger()->log($data, "INCOMING", $this->getServer());

		foreach($this->getListeners() as $name => $listener){
			if($listener->getKeywords() == $args[1] || $listener->getKeywords() == "ANY"){
				$this->executeListener($data, $args, $name);
			}
		}

		if(strpos($data, "Welcome") && !$this->joined){
			foreach($this->getChannels() as $ch){
				$this->getConnection()->sendData("JOIN $ch");
			}
			$reader = new Reader($this->getConnection());
			$reader->start();
			$this->joined = true;
		}


		if($args[0] == "PING"){
			$this->getConnection()->sendData("PONG $args[1]", false);
			$log = false;
		}

		if(isset($args[1])){
			switch($args[1]){
				case "PRIVMSG":
					$source = $args[2];
					if($args[3]{0} == ":") $args[3] = substr($args[3], 1);
					$msg = array_slice($args, 3);
					if ($source == $this->getNick()) $source = $this->getUser($data);

					if(preg_match("\01ACTION(.*)\01", $data, $matches) == false){
						$this->getLogger()->log(Terminal::$COLOR_PURPLE . Terminal::$FORMAT_BOLD . "[$args[2]] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_DARK_AQUA . "<" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . "> " . implode(" ", array_slice($args, 3)), "INCOMING", $this->getServer());
					} else {
						$this->getLogger()->log(Terminal::$COLOR_PURPLE . Terminal::$FORMAT_BOLD . "[$args[2]] " . Terminal::$FORMAT_RESET . Terminal::$COLOR_DARK_AQUA . "*" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " " . $matches[1], "INCOMING", $this->getServer());
					}


					if(stripos($msg[0], $this->getPrefix()) === 0){
						$command = ucfirst(strtolower(substr($msg[0], strlen($this->getPrefix()))));
						if($this->getCommand($command) !== null){
							$this->executeCommand($data, array_slice($args, 4), $source, $command);
							break;
						} else {
							$this->getConnection()->sendData("PRIVMSG $source :Unknown command");
							break;
						}
					}
					$log = false;
					break;

				case "NOTICE":
					if($args[3]{0} == ":") $args[3] = substr($args[3], 1);
					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "--" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . "--" . ($args[2]{0} == "#" ? Terminal::$COLOR_PURPLE . " [$args[2]] " . Terminal::$COLOR_DARK_AQUA : " ") . implode(" ", array_slice($args, 3)), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "JOIN":
					if(isset($args[3]))
						if($args[3]{0} == ":") $args[3] = substr($args[3], 1);

					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has joined " . Terminal::$COLOR_PURPLE . $args[2] . Terminal::$COLOR_WHITE, "INCOMING", $this->getServer());
					$log = false;
					break;

				case "PART":
					if(isset($args[3]))
						if($args[3]{0} == ":") $args[3] = substr($args[3], 1);

					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has left " . Terminal::$COLOR_PURPLE . $args[2] . Terminal::$COLOR_GREEN . (isset($args[3]) ? " (" . implode(" ", array_slice($args, 3)) . ")" : ""), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "QUIT":
					if(isset($args[2]))
						if($args[2]{0} == ":") $args[2] = substr($args[2], 1);

					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has quit" . Terminal::$COLOR_GREEN . (isset($args[2]) ? " (" . implode(" ", array_slice($args, 2)) . ")" : ""), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "MODE":
					$this->getLogger()->log(Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " sets mode: " . Terminal::$COLOR_YELLOW . $args[3] . Terminal::$COLOR_AQUA . (isset($args[4]) ? " on " . Terminal::$COLOR_BLUE . $args[4] . Terminal::$COLOR_AQUA : "") . " in " . Terminal::$COLOR_PURPLE . $args[2], "INCOMING", $this->getServer());
					$log = false;
					break;

				case "NICK":
					$this->getLogger()->log(Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_AQUA . " is now known as ". Terminal::$COLOR_GREEN . substr($args[2], 1), "INCOMING", $this->getServer());
					break;

				case "001":
				case "002":
				case "003":
				case "251":
				case "252":
				case "253":
				case "254":
				case "255":
				case "256":
				case "250":
				case "265":
				case "266":
					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "--" . Terminal::$COLOR_RED  . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . "-- " . trim(implode(" ", array_slice($args, 3)), ":"), "INCOMING", $this->getServer());
					$log = false;
					break;
				case "004":
				case "005":
				case "372":
				case "376":
				case "375":
				case "366":
					$log = false;
					break;

				case "332":
					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "Topic for " . Terminal::$COLOR_PURPLE . $args[3] . Terminal::$COLOR_DARK_AQUA . ": \"" . Terminal::$COLOR_YELLOW . substr(implode(" ", array_slice($args, 4)), 1) . Terminal::$COLOR_DARK_AQUA . "\"", "INCOMING", $this->getServer());
					$log = false;
					break;

				case "333":
					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "Topic for " . Terminal::$COLOR_PURPLE . $args[3] . Terminal::$COLOR_DARK_AQUA . " was set by " . Terminal::$COLOR_GREEN . $args[4] . Terminal::$COLOR_AQUA . " on " . date("l jS F \@ g:i A", intval($args[5])), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "328":
					$this->getLogger()->log(Terminal::$COLOR_RED . $args[3] . Terminal::$COLOR_DARK_AQUA . " URL: " . Terminal::$COLOR_YELLOW . substr($args[4], 1), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "396":
					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . trim(implode(" ", array_slice($args, 3)), ":"), "INCOMING", $this->getServer());
					$log = false;
					break;

				case "353":
					$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "Users on " . Terminal::$COLOR_PURPLE . $args[4] . Terminal::$COLOR_DARK_AQUA . " are: " . Terminal::$COLOR_YELLOW . substr(implode(" | ", array_slice($args, 5)), 1), "INCOMING", $this->getServer());
					$log = false;
					break;
				default:
					break;
				}
			}

			 if(!isset($log)) $this->getLogger()->log($data, "INCOMING", $this->getServer());
		$this->main();
	}
}