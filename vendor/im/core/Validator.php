<?php

namespace im\core;


class Validator{

    private static $err = [];

    private static $rules = [
        'email' => '/^([.a-z0-9_-]+)@([.a-z0-9-_]+)\.([a-z0-9_-]{1,6})$/i',
        'nameFull' => '/^([.a-zа-яА-ЯёЁ0-9_-]{2,20}) ([.a-zа-яА-ЯёЁ0-9_-]{2,20}) ([.a-zа-яА-ЯёЁ0-9_-]{2,20})$/iu',
        'phone' => '/^([.a-zа-яА-ЯёЁ0-9_+\-( ,)\/]{4,50})$/iu',
        'note' => '/^([.a-zа-яА-ЯёЁ0-9_+\-( ,)\/]{0,350})$/iu',
        'word' => '/^([.a-zа-яА-ЯёЁ0-9_+\-( ,)\/]{3,50})$/iu',
        'adress' => '/^([.a-zа-яА-ЯёЁ0-9_+\-( ,)\/]{10,250})$/iu'
    ];

    /**
     * Первичное обеззараживание, применяется ко всему тексту, в котором не нужны спец символы
     * @param $inVal
     * @return string
     */

    private static function textCleaner($inVal){
        $res1 = trim(strip_tags($inVal));
        if($res1 == '' || $res1 == null){
            return false;
        }else{
            return $res1;
        }
    }

    /**
     * Первичное обеззараживание, применяется к e-mail
     * @param $inVal
     * @return string
     */

    private static function emailCleaner($inVal){
        $res1 = trim($inVal);
        if($res1 == '' || $res1 == null){
            return false;
        }else{
            return $res1;
        }
    }

    /**
     * Первичное обеззараживание, применяется к примечаниям, в котором не нужны спец символы, но они могут быть пусты
     * @param $inVal
     * @return string
     */

    private static function noteCleaner($inVal){
        $res1 = trim(strip_tags($inVal));
            return $res1;
    }

    /**
     * Общий валидатор
     * @param $inVal
     * @param $type
     */
    private static function validate($inVal, $regStr){
        $res1 = preg_match($regStr, $inVal);
        return (Boolean)$res1;
    }

    public static function getRegStr($inName){
        if (isset(self::$rules[$inName])){
            return self::$rules[$inName];
        }else{
            return false;
        }
    }

    public static function email($inVal, $message=''){
        $res1 = self::emailCleaner($inVal);
        $res2 = self::validate($res1, self::$rules['email']);
        if($res2 === false){
            return $message;
        }else{
            return $res2;
        }
    }

    public static function name($inVal, $message=''){
        $res1 = self::textCleaner($inVal);
        $res2 = self::validate($res1, self::$rules['nameFull']);
        if($res2 === false){
            return $message;
        }else{
            return $res2;
        }
    }
    public static function word($inVal, $message=''){
        $res1 = self::textCleaner($inVal);
        $res2 = self::validate($res1, self::$rules['word']);
        if($res2 === false){
            return $message;
        }else{
            return $res2;
        }
    }
    public static function adress($inVal, $message=''){
        $res1 = self::textCleaner($inVal);
        $res2 = self::validate($res1, self::$rules['adress']);
        if($res2 === false){
            return $message;
        }else{
            return $res2;
        }
    }
    public static function phone($inVal, $message=''){
        $res1 = self::textCleaner($inVal);
        $res2 = self::validate($res1, self::$rules['phone']);
        if($res2 === false){
            return $message;
        }else{
            return $res2;
        }
    }
    public static function note($inVal, $message=''){
        $res1 = self::noteCleaner($inVal);
        $res2 = self::validate($res1, self::$rules['note']);
        if($res2 === false){
            return $message;
        }else{
            return $res2;
        }
    }

}