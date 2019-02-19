<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 04.11.2018
 * Time: 10:09
 */

namespace im\core;

class Regisrty
{
    /**
     * Используем трейт Синглтон
     */
    use TSingleton;

    public static $objects = [];
    protected static $properties = [];

    protected function __construct(){
        $config = require_once ROOT . '/config/config.php';
        //debug($config);
        foreach($config['components'] as $name => $component){
            self::$objects[$name] = new $component;
        }
    }

    public function setProperty($name, $value){
        self::$properties[$name] = $value;
    }

    public function getProperty($name){
        if(isset(self::$properties[$name])){
            return self::$properties[$name];
        }
        return null;
    }

    public function getProperties(){
        return self::$properties;
    }

    public function __get($name){
        if(is_object(self::$objects[$name])){
            return self::$objects[$name];
        }
    }

    public function __set($name, $object){
        if(!isset(self::$objects[$name])){
            self::$objects[$name] = new $object;
        }
    }

    public function getList(){
        echo '<pre>';
        var_dump(self::$objects);
        echo '</pre>';
    }

    }