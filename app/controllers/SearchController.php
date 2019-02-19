<?php
namespace app\controllers;

use app\models\Search;
use im\core\base\View;
use im\libs\Timer;

class SearchController extends AppController{

    public function indexAction(){
        Timer::start();
        $query = !empty(trim($_GET['s'])) ? trim($_GET['s']) : null;
        $query = strip_tags($query);
        $query = h($query);

        if($query){
            $model = new Search();
            $products = $model->getResults($query);
        }
        View::setMeta('Поиск по: ' . h($query));

        $this->set(compact('products', 'query'));
        echo Timer::finish() . ' сек.';
    }
}