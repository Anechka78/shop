<?php
namespace app\controllers;

/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 30.10.2018
 * Time: 18:04
 */
class PostsController extends AppController
{
    public function indexAction(){
        echo'Posts::index';
    }

    public function testAction(){
        //debug($this->route);
        echo'Posts::test';
    }

    public function testPageAction(){
        echo'Posts::testPage';
    }
}