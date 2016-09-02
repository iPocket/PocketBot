<?php
namespace Command;

class Title extends \Library\Command\Base {

    public $name = "Title";
    protected $help = 'Get the title of a URL';
    protected $usage = 'title <url>';
    protected $count = 1;
    private $str;

    public function exec() {
        $argument = str_replace(array("\r", "\n"), '', $this->args[0]);
        if($this->exists($argument)){
            if(empty($this->getTitle($argument))){
                $this->say(ERROR . $this->str['errmsg'] . ". Error " . $this->str['errno']);
            } else {
                $this->say("12Title: " . $this->getTitle($argument));
            }
        } else {
            $this->say(ERROR . "URL Does not exist");
        }
    }

    private function getTitle($url){
        $str = $this->str['content'];
        if(strlen($str)>0){
            $str = trim(preg_replace('/\s+/', ' ', $str));
            preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title);
            if(isset($title[1])){
                return $title[1];
            } else {
                return ERROR . "No Title for that URL";
            }
        }
    }

    private function getPage($url){

        $options = array(
            CURLOPT_CUSTOMREQUEST  => "GET",       
            CURLOPT_POST           => false,
            CURLOPT_RETURNTRANSFER => true,    
            CURLOPT_HEADER         => false,   
            CURLOPT_FOLLOWLOCATION => true,     
            CURLOPT_ENCODING       => "",     
            CURLOPT_AUTOREFERER    => true,     
            CURLOPT_CONNECTTIMEOUT => 10,     
            CURLOPT_TIMEOUT        => 10,     
            CURLOPT_MAXREDIRS      => 10,      
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FTP_SSL => CURLFTPSSL_TRY
        );

        $ch      = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err     = curl_errno($ch);
        $errmsg  = curl_error($ch);
        $header  = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    private function exists($url){
        $str = $this->getPage($url);
        if($str['errno'] == 404){
            return false;
        }
        file_put_contents("lastweb.txt", $str['content']);
        $this->str = $str;
        return true;
    }
}