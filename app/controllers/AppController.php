<?php
/**
 * Класс для создания общего для всех контроллеров функционала
 */

namespace app\controllers;

use im\core\App;
use im\core\base\Controller;
use im\core\Regisrty;
use im\widgets\currency\Currency;
use im\widgets\menu\MenuModel;

class AppController extends Controller
{
   // public $meta = [];

    public function __construct($route){
        parent::__construct($route);
        $currs = Currency::getCurrencies();
        //debug($route);

        $code = [];
        foreach($currs as $k => $v){
             $code[] = $v['code'];
        }

        $curr = array_combine($code, $currs);
        //debug($curr);

        //setcookie('currency', '2', time()+3600, '/');
        App::$app->setProperty('currencies', $curr);
        //debug(App::$app->getProperties());
        App::$app->setProperty('currency', Currency::getCurrency(App::$app->getProperty('currencies')));
        //debug(App::$app->getProperties());
        $model = new MenuModel();
        $cats = $model->findAll();
        $id = [];
        foreach($cats as $k => $v){
            $id[] = $v['id'];
        }
        $categories = array_combine($id, $cats);
        //debug($categories);
        App::$app->setProperty('cats', $categories);
        //debug(App::$app->getProperties());

    }



    /**
     * Добавление мета-тегов
     * @param string $title
     * @param string $desc
     * @param string $keywords
     */
    /*public function setMeta($title = '', $desc = '', $keywords = ''){
        $this->meta['title']    = $title;
        $this->meta['desc']     = $desc;
        $this->meta['keywords'] = $keywords;
    }*/
}