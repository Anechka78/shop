<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 04.11.2018
 * Time: 11:23
 */

namespace im\core;

use im\core\Regisrty;

class App
{
    public static $app;

    public function __construct()
    {
        session_start();
        //echo'qty = '; var_dump($_SESSION['cart']['products']); //[$key]['qty']);

        //контейнер, в котором записан объект нашего реестра, в котором мы храним св-ва и классы для автоподключения
        self::$app = Regisrty::instance();
        $this->getParams();
    }

    //Метод, который получает настройки из реестра и контейнера, в котором есть св-ва магазина
    protected function getParams()
    {
        $params = require_once CONF . '/params.php';
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                self::$app->setProperty($k, $v);
            }

        }
    }


}