<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 14.11.2018
 * Time: 11:19
 */

namespace app\controllers\admin;


class TestController extends AppController
{
    //главная страница админки
    public function indexAction(){
    echo __METHOD__;

}

    public function testAction(){
    echo __METHOD__;

}
}