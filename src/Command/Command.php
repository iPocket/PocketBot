<?php 
namespace Command;

abstract class Command {

	protected $name = '';
	private $bot = null;
	private $plugin = null;
	private $args = [];
	private $source = null;
	private $data;
	protected $level = 0;
	protected $amount = 0;
	protected $help = '';
	protected $usage = '';

	public function execute($data, $args, $source){

		$this->source = (string) $source;
		$this->data = (string) $data;
		$this->args = (array) $args;

		if($this->getPerm() < $this->getLevel()){
			$this->say(ERROR . "Permission denied, you need at least access level {$this->getLevel()} (" . $this->format($this->getLevel()) . ")!");
			return;
		}

		if(!is_array($this->getAmount())){
			if($this->getAmount() !== -1 && count($this->getArgs()) < $this->getAmount()){
				$this->say(ERROR . "You need to specify at least {$this->getAmount()} arguments.");
				return;
			}
		} else {
			if(!in_array(count($this->getArgs()), $this->getAmount()) && !in_array(-1, $this->getAmount())){
				$this->say(ERROR . "You need to specify " . implode(" or ", $this->getAmount()) . " arguments.");
				return;
			}
		}
		$this->exec();
	}

	protected function getPerm(){

        $hosts = explode(" ", $this->getData())[0];

        if($this->getBot()->hasLevel($hosts)){
        	return $this->getBot()->getLevels()[$hosts];
        } else {
        	return 0;
        }
	}

	protected function say($msg){
		$this->getBot()->getConnection()->sendData('PRIVMSG ' . $this->getSource() . ' :' . $msg);
	}

	protected function getHelp(){
		if (!empty($this->help)) return array($this->help, $this->usage);
	}

	public function exec(){
		flush();
        throw new \Exception('You have to overwrite the "exec" method and the "execute". Call the parent "execute" and execute your custom "exec".');
	}

	protected function getNick(){
		$dat = explode("!", $this->getData());
		return trim($dat[0], ":");
	}

	public function setBot($bot){
		$this->bot = $bot;
	}

	public function getBot(){
		return $this->bot;
	}

	public function getData(){
		return (string) $this->data;
	}

	public function getArgs(){
		return (array) $this->args;
	}

	public function getSource(){
		return (string) $this->source;
	}

	public function getLevel(){
		return (int) $this->level;
	}

	public function getAmount(){
		return $this->amount;
	}

	public function getName(){
		return (string) $this->name;
	}

	public function getPlugin(){
		return $this->plugin;
	}

	public function setPlugin(\Plugin\Plugin $p){
		$this->plugin = $p;
	}

	public function format($l){
		if($l == 0) return "User";
		if($l == 1) return "Moderator";
		if($l == 2) return "Adminstrator";
		if($l == 3) return "Co-Owner";
		if($l == 4) return "Owner";
		return null;
	}
}