<?php
    class Autoloader {

        public static function load($class) {
            $filename = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
            if (file_exists($filename)) {
                return require $filename;
            }
            throw new \Exception('File: "' . $filename . '" not found.');
        }

    }
?>
