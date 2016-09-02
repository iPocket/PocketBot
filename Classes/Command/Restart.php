<?php
namespace Command;

class Restart extends \Library\Command\Base {

    public $name = "Restart";
    protected $help = 'Makes me restart';
    protected $usage = 'restart';
    protected $amount = 0;
    public $level = 3;
   
    public function exec() {
        $this->connection->sendData("QUIT :Restart requested by " . $this->getNick());
        $this->bot->server == "irc.freenode.net" ? exec('start "" bin\mintty.exe -o Columns=88 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="Consolas" -o FontHeight=10 -o CursorType=0 -o CursorBlinks=1 -h error -t "PocketBot" -i bin/pocketmine.ico -w max php start.php config --enable-ansi %*') : exec('start "" bin\mintty.exe -o Columns=88 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="Consolas" -o FontHeight=10 -o CursorType=0 -o CursorBlinks=1 -h error -t "PocketBot" -i bin/pocketmine.ico -w max php start.php zandronum --enable-ansi %*');
        exit;
    }
}