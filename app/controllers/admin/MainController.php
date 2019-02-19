<?php

namespace app\controllers\admin;


use im\core\base\View;

class MainController extends AppController{

    public function indexAction(){
        View::setMeta('Панель управления', 'Описание страницы', 'Ключевые слова');

    }

}