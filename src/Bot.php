<?php

use Connection\Client;
use Utils\Terminal;

class Bot {

	private $settings = [];

	private $connection = null;
	private $logger = null;

	private $plugins = [];
	private $commands = [];
	public $aliases = [];
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
	private $perms = [];
	private $ssl = false;

	private $counter = 0;


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
		$this->perms = (array) $settings['perms'];
		$this->logger = $logger;
		$this->connection = new Client($this);
	}

	public function init(){
		$this->joined = false;
		$this->getLogger()->log("Loading plugins...", "Info", "Main");
		foreach(glob(ROOT_DIR . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "*.php") as $p){
			$plugin = "plugins\\" . basename($p, ".php");
			$p = new $plugin();
			if($p instanceof \Plugin\Plugin)
				$this->addPlugin($p);
		}
		$this->addPlugin(new \Plugin\CorePlugin());
		$this->getConnection()->connect();
		$this->main();
	}

	public function getNick(){
		return $this->nick;
	}

	public function setNick($nick){
		$this->nick = (string) $nick;
		$this->getConnection()->sendData("NICK $nick");
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
		return "Server";
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
		$this->getLogger()->log("Loading plugin " . Terminal::$COLOR_GREEN . $p->getName() . Terminal::$COLOR_WHITE . " v" . Terminal::$COLOR_YELLOW . $p->getVersion() . Terminal::$COLOR_WHITE . " by " . Terminal::$COLOR_RED . $p->getAuthor() . Terminal::$COLOR_WHITE . "...", "Info", "Main");
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
		if(isset($c->aliases)){
			foreach($c->aliases as $a){
				$this->aliases[$a] = $c->getName();
			}
		}
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

	public function getPerms(){
		return $this->perms;
	}

	public function getPerm($host){
		return isset($this->perms[(string) $host]) ? $this->perms[(string) $host] : null;
	}

	public function addPerm($host, $level){
		$this->perms[(string) $host] = (int) $level;
	}

	public function removePerm($host){
		unset($this->perms[(string) $host]);
	}

	public function hasPerm($l){
		return isset($this->perms[$l]);
	}

	public function useSSL(){
		return $this->ssl;
	}

	public function getSettings(){
		return $this->settings;
	}

	public function rainbow($msg){
		$count = -1;
		$rainbow = array(4, 7, 8, 9, 11, 12, 6);
		$info = str_split($msg);
		$info = str_replace("\003", "", $info);
		$msg = [];
		foreach($info as $m){
			$c = $rainbow[$count + 1];
			if($m !== " " && $m !== "\003" && $m !== "" && $m !== "\017" && $m !== "\02" && $m !== "\026" && $m !== "\01" && $m !== "\037"){
				$msg[] = (string) "\003" . (strlen((string)$c) == 1 ? 0 . $c : $c) . "‌‌" . $m;
				$count++;
			} elseif($m == " ") {
				$msg[] = " ";
			} elseif($m == "\003"){
				$msg[] = "";
			} else {
				$msg[] = "";
			}
			if($count == 6){
				$count = -1;
			}
		}

		return implode("", $msg);
    }

	public function getUser($data){
		$dat = explode("!", $data);
		return isset($dat[0]) ? trim($dat[0], ":") : $data;
	}

	public function evaluate($p){
		try {
			ob_start();
			eval($p);
			return [true, ob_get_clean()];
		} catch (\ParseError $e){
			ob_end_clean();
			return [false, $e->getMessage()];
		}
	}

	private function main(){
		do {
			$data = $this->getConnection()->getData();
			$args = explode(" ", $data);

			if(empty($data)){
				$this->main();
				return;
			}

			if($data{0} == ":") $data = substr($data, 1);

			foreach($this->getListeners() as $name => $listener){
				if($listener->getKeywords() == $args[1] || $listener->getKeywords() == "ANY"){
					$this->executeListener($data, $args, $name);
				}
			}

			if(strpos($data, "Welcome") && !$this->joined){
				foreach($this->getChannels() as $ch){
					$this->getConnection()->sendData("JOIN $ch");
				}
				$reader = new Reader($this);
				$reader->start();
				$this->joined = true;
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
							if(isset($this->aliases[$command])) $command = $this->aliases[$command];
							if($this->getCommand($command) !== null){
								$this->executeCommand($data, array_slice($msg, 1), $source, $command);
							} else {
								$this->getConnection()->sendData("PRIVMSG $source :{$this->getUser($args[0])}: Unknown command");
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

						if($this->getUser($args[0]) == $this->getNick()) $this->channels[$args[2]] = null;
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
						$this->getLogger()->log(Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " is now known as ". Terminal::$COLOR_GREEN . substr($args[2], 1), "INCOMING", $this->getServer());
						$log = false;
						break;

					case "KICK":
						$this->getLogger()->log(Terminal::$COLOR_PURPLE . "[" . $args[2] . "] " . Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has kicked ". Terminal::$COLOR_GREEN . $args[3] . (isset($args[4]) ? Terminal::$COLOR_DARK_AQUA . " (" . Terminal::$COLOR_AQUA . substr(implode(" ", array_slice($args, 4)), 1) . Terminal::$COLOR_DARK_AQUA . ")" : ''), "INCOMING", $this->getServer());
						$log = false;
						break;

					case "TOPIC":
						$this->getLogger()->log(Terminal::$COLOR_PURPLE . "[" . $args[2] . "] " . Terminal::$COLOR_RED . $this->getUser($args[0]) . Terminal::$COLOR_DARK_AQUA . " has changed topic to " . Terminal::$COLOR_DARK_AQUA . "\"" . Terminal::$COLOR_AQUA . substr(implode(" ", array_slice($args, 3)), 1) . Terminal::$COLOR_DARK_AQUA . "\"", "INCOMING", $this->getServer());
						$log = false;
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
					case "481":
					case "461":
					case "477":
					case "421":

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
						$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "Topic for " . Terminal::$COLOR_PURPLE . $args[3] . Terminal::$COLOR_DARK_AQUA . " was set by " . Terminal::$COLOR_GREEN . $this->getUser($args[4]) . Terminal::$COLOR_DARK_AQUA . " on " . Terminal::$COLOR_AQUA . date("l jS F \@ g:i A", intval($args[5])), "INCOMING", $this->getServer());
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

					case "433":
						$this->counter++;
						$this->getLogger()->log(Terminal::$COLOR_DARK_AQUA . "Nickname {$args[3]} already in use, retrying with {$this->getNick()}{$this->counter}...", "INCOMING", $this->getServer());
						$this->getConnection()->sendData("NICK " . $this->getNick() . $this->counter);
						$log = false;
						break;
					default:
						$log = true;
						break;
					}
				}

				if($args[0] == "PING"){
					$this->getConnection()->sendData("PONG $args[1]", false);
					$log = false;
				}

		 if($log == true) $this->getLogger()->log($data, "INCOMING", $this->getServer());
		} while(true);
	}
}