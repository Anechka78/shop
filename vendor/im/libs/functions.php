<?php

function debug($arr, $name=NULL){
    if ($name)
        echo '</br>'.'>>> '.$name.':'.'</br>';
    echo '<pre>'. print_r($arr, true) .'</pre>';
}

/**
 * @param bool|false $http URL, на который мы хотим перенаправить пользователя
 */
function redirect($http = false){
    if($http){
        $redirect = $http;
    }else{
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    }
    header("Location: $redirect");
    die;
}

function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}