<?php

namespace im\core;
/**
 * Трейт для создания класса типа Синглтон
 * Trait TSingleton
 * @package vendor\core
 */

trait TSingleton{

    protected static $instance;

    public static function instance(){
        if(self::$instance === null){ //если св-во пусто
            self::$instance = new self; //положим в него объект данного класса
        }
        return self::$instance; //вернем этот объект
    }

}