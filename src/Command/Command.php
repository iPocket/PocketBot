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
	protected $secret = false;

	public function execute($data, $args, $source){

		$this->source = (string) $source;
		$this->data = (string) $data;
		$this->args = (array) $args;

		if($this->getPerm() < $this->getLevel()){
			$this->say($this->getNick() . ": Permission denied, you need at least access level {$this->getLevel()} (" . $this->format($this->getLevel()) . ")!");
			return;
		}

		if(!is_array($this->getAmount())){
			if($this->getAmount() !== -1 && count($this->getArgs()) < $this->getAmount()){
				$this->say($this->getNick() . ": You need to specify at least {$this->getAmount()} arguments.");
				return;
			}
		} else {
			if(!in_array(count($this->getArgs()), $this->getAmount()) && !in_array(-1, $this->getAmount())){
				$this->say($this->getNick() . ": You need to specify " . implode(" or ", $this->getAmount()) . " arguments.");
				return;
			}
		}
		$this->exec();
	}

	protected function getPerm(){

        $hosts = explode(" ", $this->getData())[0];

        if($this->getBot()->hasPerm($hosts)){
        	return $this->getBot()->getPerm($hosts);
        } elseif($hosts == "Console"){
        	return 4;
        } else {
        	return 0;
        }
	}

	protected function say($msg){
		if($this->getSource() !== "Console")
			$this->getBot()->getConnection()->sendData('PRIVMSG ' . $this->getSource() . ' :' . $msg);
		else
			$this->getBot()->getLogger()->log($msg, "INFO", "CommandReader");
	}

	protected function notice($msg){
       	if($this->getSource() !== "Console")
			$this->getBot()->getConnection()->sendData('NOTICE ' . $this->getNick() . ' :' . $msg);
		else
			$this->getBot()->getLogger()->log($msg, "INFO", "CommandReader");
    }

	public function getUsage(){
		return $this->usage;
	}

	public function getHelp(){
		return $this->help;
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

	public function isSecret(){
		return (bool) $this->secret;
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