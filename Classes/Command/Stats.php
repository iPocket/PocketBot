<?php
namespace Command;

class Stats extends \Library\Command\Base {

    public $name = "Stats";
    protected $help = 'Shows stats of the bot';
    protected $usage = 'stats';
    protected $amount = 0;
    public $level = 0;
   
    public function exec() {
    	global $errors;
    	$time = $this->format(microtime(true) - START_TIME);
        $this->say("I've been running since " . date("l jS F \@ h:m:s A", START_TIME) . " and been running for " . $time . " and sent " . $this->bot->upload . " Bytes of data to server and recieved " . $this->bot->download . " Bytes of data from server with " . ($errors == 0 ? "no" : $errors) . " error(s) occured.");
    }

    private function format($seconds){

    	$weeks = (floor($seconds / (60 * 60) / 24)) / 7;

    	$days = (floor($seconds / (60 * 60) / 24)) % 7;

    	$hours = floor($seconds / (60 * 60));

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