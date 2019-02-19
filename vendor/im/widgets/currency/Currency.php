<?php


namespace im\widgets\currency;

use im\core\App;

class Currency
{
    protected  $tpl; //шаблон для вывода списка валют
    protected  $currencies; // список валют
    protected  $currency; //активная валюта

    public function __construct(){
        $this->tpl = __DIR__ . '/currency_tpl/currency.php';
        $this->run();
    }

    protected function run(){
        $this->currencies = App::$app->getProperty('currencies');
        $this->currency = App::$app->getProperty('currency');
        echo $this->getHtml();
    }

    public static function getCurrencies(){
        $model = new CurrencyModel();
        
        $sort = 'base';
        return $model->findCurrencies($sort);
    }

    //вывод активной валюты
    public static function getCurrency($currencies){
        if(isset($_COOKIE['currency']) && array_key_exists($_COOKIE['currency'], $currencies)){
            $key = $_COOKIE['currency'];
        }else{
            $key = key($currencies);//возвращает текущий первый элемент массива, те базовую валюту
        }
        $currency = $currencies[$key];
        return $currency;
    }

    //метод, формирующий html-разметку
    protected function getHtml(){
        ob_start();
        require_once $this->tpl;
        return ob_get_clean();
    }

}