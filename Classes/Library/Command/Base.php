<?php 
namespace Library\Command;

abstract class Base {

	public $name = '';
	protected $connection = null;
	protected $bot = null;
	protected $args = [];
	protected $source = null;
	protected $data;
	public $level = 0;
	protected $amount = 0;
	protected $help = '';
	protected $usage = '';

	public function execute(array $args, $source, $data){

		$this->source = $source;
		$this->data = $data;
		$this->args = $args;

		if(!(isset($this->bot->allowed[$source])) && $this->getPerm() < 3 && $this->bot->listen == false && !(isset($this->bot->allowed[$this->getNick()]))) return;

		if($this->getPerm() < $this->level){
			$this->say(ERROR . "Permission denied, you need at least access level $this->level (" . $this->getLevel($this->level) . ")!");
			return;
		}

		if(!is_array($this->amount)){
			if($this->amount !== -1 && count($args) < $this->amount){
				$this->say(ERROR . "You need to specify at least $this->amount arguments.");
				return;
			}
		} else {
			if(!in_array(count($args), $this->amount) && !in_array(-1, $this->amount)){
				$this->say(ERROR . "You need to specify " . implode(" or ", $this->amount) . " arguments.");
				return;
			}
		}
		$this->exec();
	}

	protected function getPerm(){

        $hosts = explode(" ", $this->data)[0];

        if(isset($this->bot->levels[$hosts])){
        	return $this->bot->levels[$hosts];
        } else {
        	return 0;
        }
	}

	protected function say($msg){
		$this->connection->sendData('PRIVMSG ' . $this->source . ' :' . $msg);
	}

	protected function getHelp(){
		if (!empty($this->help)) return array($this->help, $this->usage);
	}

	public function exec(){
		flush();
        throw new \Exception( 'You have to overwrite the "exec" method and the "execute". Call the parent "execute" and execute your custom "exec".' );
	}

	protected function getNick(){
		$dat = explode("!", $this->data);
		return trim($dat[0], ":");
	}

	public function setConnection($connection){
		$this->connection = $connection;
	}

	public function setBot($bot){
		$this->bot = $bot;
	}

	public function getLevel($l){
		if($l == 0) return "User";
		if($l == 1) return "Moderator";
		if($l == 2) return "Adminstrator";
		if($l == 3) return "Co-Owner";
		if($l == 4) return "Owner";
	}
}