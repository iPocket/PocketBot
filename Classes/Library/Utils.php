<?php
namespace Library;

class Utils {

	public static function getText($text){
		return str_replace(array("\r", "\n"), '', $text);
	}

	public static function getMessage($msg){
		return str_replace(array("\r", "\n"), '', array_slice( $msg, 3 ));
	}

	public static function getClassName($object) {
        $objectName = explode('\\', get_class($object));
        $objectName = $objectName[count($objectName) - 1];
        return $objectName;
    }

    public static function toANSI($msg){
        return "$msg";
    }
}