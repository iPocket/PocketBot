<?php
namespace Library;

class Bot {

	public $nick = '';
	public $name = '';
	private $password = '';
	public $server = '';
	public $port = 0;
	public $connnection;
	public $channels = [];
	public $prefix = '';
	public $commands = [];
	public $listen = true;
	public $levels = [];
	public $allowed = [];
	public $logger = null;
	public $download = 0;
	public $upload = 0;

	public function __construct($config = array()){
		$this->connection = new Connection\Client($this);
		if(count($config) === 0) return;

		$this->setConfig($config);

		$this->logger = new Logger();
	}

	public function __destruct(){

	}

	public function connect(){
		global $config;
		foreach($config['channels'] as $ch){
			$this->allowed[$ch] = $ch;
		}
		$this->connection->joined = false;
		$this->connection->connect();
		$this->main();
	}

	private function main(){
		global $config;
		global $errors;
		echo "\x1b]0;PocketBot | " . ucfirst(explode(".", $config['server'])[1]) . " | Download: $this->download Bytes | Upload: $this->upload Bytes | $errors Errors occured\x07";
		$data = $this->connection->getData();
		$args = explode(" ", $data);

		if(!is_resource($this->connection->socket)){
			$this->connect();
			return;
		}

		$out = explode(" ", $data);
		if(isset($out[0])) $out[0] = "\x1b[38;5;214m$out[0]\x1b[m";
		if(isset($out[1])) $out[1] = "\x1b[38;5;83m$out[1]\x1b[m";
		if(isset($out[2])) $out[2] = "\x1b[38;5;227m$out[2]\x1b[m\x1b[38;5;127m";

		if(!empty($data)) $this->logger->log(implode(" ", $out), "INCOMING");

		if(stripos($data, 'Welcome') !== false && !$this->connection->joined){
			foreach($this->channels as $channel){
				$this->connection->sendData("JOIN $channel");
			}
			$reader = new \Library\Reader($this->connection->socket);
        	$reader->start();
			$this->connection->joined = true;
		}

		if($args[0] == "ERROR"){
			if($args[1] == ":Closing"){
				if(strtolower($args[2]) == "link:"){
					exit(255);
				}
			}
		}
		if($args[0] == 'PING') $this->connection->sendData("PONG $args[1]");
		if(isset($args[1])){
			if($args[1] == "PRIVMSG"){
				$source = $args[2];

				if ($source == $this->nick){
                    $source = $this->getUserNickName($data);
                    $pm = true;
                } else {
                    $pm = false;
                }

				$cmd = str_replace(array(":", "\r", "\n"), "", $args[3]);
				$cmd = ucfirst(strtolower($cmd));
				$command = str_replace(array(":", "\r", "\n", $config['prefix']), "", $args[3]);
				$command = ucfirst(strtolower($command));

				if($command == ".") $command = "Eval";
				if($command == "=") $command = "Return";
				if($command == "Commands") $command = "Help";
				if(isset($this->commands[$command]) && stripos($args[3], $config['prefix']) !== false){
					$this->executeCommand( $source, $command, array_slice( $args, 4 ), $data);
				} elseif(stripos($cmd, $config['prefix']) !== false){
					$this->connection->sendData("PRIVMSG $source :" . ERROR . "Unknown Command");
				} elseif($pm == true && $source !== "PocketBot"){
					if(isset($this->commands[$command])){
						$this->executeCommand( $source, $command, array_slice( $args, 4 ), $data);
					} else {
						$this->connection->sendData("PRIVMSG $source :" . ERROR . "Unknown Command");
					}
				}

				if($args[3] == ":\01VERSION\01"){
					$this->connection->sendData("NOTICE " . $this->getUserNickName($data) . " :\01VERSION PocketKiller's PHP Bot\01");
				}
			}
		}
		$this->main();
	}

	public function setConfig($config){
		$this->server = $config['server'];
		$this->port = $config['port'];
		$this->name = $config['name'];
		$this->nick = $config['nick'];
		$this->password = $config['password'];
		$this->channels = $config['channels'];
		$this->prefix = $config['prefix'];
		$this->levels = $config['levels'];
		return;
	}

	public function addCommand(\Library\Command\Base $command){
		global $config;
		$commandName = $command->name;
        $command->setConnection($this->connection);
        $command->setBot($this);
        $this->commands[$commandName] = $command;
        $this->logger->log("Command Added: $commandName", "INFO");
	}

	protected function executeCommand( $source, $commandName, array $arguments, $data) {
        $command = $this->commands[$commandName];
        $command->execute($arguments, $source, $data);
    }

	public function getUserNickName($data){
		$data = explode("!", $data);
		return trim($data[0], ":");
	}

	public function rainbow($msg){
		$count = -1;
		$rainbow = array(4, 7, 8, 9, 11, 12, 6, 13);
		$info = Utils::getText(str_split($msg));
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
			if($count == 7){
				$count = -1;
			}
		}

		return implode("", $msg);
    }

	public function evals($p){
        eval($p);
        return;
    }

	public function returns($p){
        return eval("return str_replace(array('\r', '\n'), ' ', print_r(" . $p . ", true));");
    }

    public function setListen($channel, $bool){
        if($bool){
            $this->allowed[$channel] = $channel;
        } elseif(isset($this->allowed[$channel])) {
            unset($this->allowed[$channel]);
        }
    }
}