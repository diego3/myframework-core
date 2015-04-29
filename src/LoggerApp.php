<?php
//namespace MyFrameWork;

use MyFrameWork\Factory;

require_once PATH_LOCAL . '/vendor/apache/log4php/src/main/php/Logger.php';
require_once PATH_LOCAL . '/vendor/apache/log4php/src/main/php/appenders/LoggerAppenderPhp.php';


class LoggerApp extends LoggerAppenderPhp {
    protected static $errors = array();
    protected static $lastError;
    
    public static function getLastError() {
        return self::$lastError;
    }
    
    public static function getErrors($type=null) {
        if (is_null($type)) {
            return self::$errors;
        }
        else if (isset(self::$errors[$type])) {
            return self::$errors[$type];
        }
        else {
            Factory::log()->info('O tipo "' . $type . '" não possui erros');
            return array();
        }
    }
    
    public static function hasError($type=null) {
        if (is_null($type)) {
            return count(self::$errors) > 0;
        }
        else if (isset(self::$errors[$type])) {
            return count(self::$errors[$type]) > 0;
        }
        return false;
    }
    
    public static function getTotalErrors() {
        $qtd = 0;
        foreach (self::$errors as $list) {
            $qtd += count($list);
        }
        return $qtd;
    }
    
    protected static function addError($level, $message) { 
        if (!array_key_exists($level, self::$errors)) {
            self::$errors[$level] = array();
        }
        self::$lastError = $message;
        self::$errors[$level][] = $message;
    }
            
    public function append(LoggerLoggingEvent $event) {        
        self::addError(strtolower($event->getLevel()->toString()), $event->getMessage());
    }
    
    public static function clear($type=null) {
        if (is_null($type)) {
            self::$lastError = null;
            self::$errors = array();
        }
        else if (isset(self::$errors[$type])) {
            unset(self::$errors[$type]);
        }
        else {
            Factory::log()->info('O tipo "' . $type . '" não foi definido');
        }
    }
}

