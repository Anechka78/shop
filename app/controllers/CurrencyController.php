<?php

namespace app\controllers;

use app\models\Cart;
use app\models\Currency;

class CurrencyController extends AppController
{
    public function changeAction(){
        $currency = !empty($_GET['curr']) ? $_GET['curr'] : null;

        if($currency){
            $model = new Currency();
            $curr = $model->findOne($currency, 'code')[0];
            //debug($curr);
            //debug($_SESSION['cart']);
            //die();
            if(!empty($curr)){
                setcookie('currency', $currency, time() + 3600*24*7, '/');
                Cart::recalc($curr);
            }
        }
        redirect();
    }
}