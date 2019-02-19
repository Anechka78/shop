<?php

namespace app\controllers\admin;


use im\core\base\View;
use im\libs\Cache;

class CacheController extends AppController{

    public function indexAction(){
        View::setMeta('Очистка кеша', '', '');
    }

    public function deleteAction(){
        $key = isset($_GET['key']) ? $_GET['key'] : null;
        $cache = new Cache();
        switch($key){
            case 'category':
                $cache->delete('cats');
                $cache->delete('site_menu');
                break;
            case 'filter':
                break;
        }
        $_SESSION['success'] = 'Выбранный кэш удален';
        redirect();
    }

}