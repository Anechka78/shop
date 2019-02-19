<?php

namespace im\core;
/**
 *
 * Time: 15:19
 */
class Router
{
    protected static $routes = []; //массив маршрутов - таблица маршрутов
    protected static $route = []; //текущий маршрут, который вызывается в URL

    public static function add($regexp, $route = []){
        self::$routes[$regexp] = $route;
    }

    /**
     * Метод, получающий всю таблицу маршрутов
     *
     * @return array
     */
    public static function getRoutes(){
        return self::$routes;
    }

    /**
     * Метод, возвращающий текущий маршрут, с которым работает приложение $route
     *
     * @return array
     */
    public static function getRoute(){
        return self::$route;
    }

    /**
     * Метод ищет совпадение url от пользователя с таблицей маршрутов
     *
     * @param $url запрос от пользователя
     * @return bool
     */
    protected static function matchRoute($url){
        //debug($url);
        foreach(self::$routes as $pattern => $route){ //ключ - регулярка в шаблоне, а значение - массив кнтрл-экшн
            if(preg_match("#$pattern#i", $url, $matches)){ //в [0] элемент записывает совпадение с строкой запроса, в [1] [2]- кнтр и экшн
                //debug($matches);
                foreach($matches as $k => $v){
                    if(is_string($k)){
                        $route[$k] = $v;
                    }
                }
                //debug($route);
                if(!isset($route['action'])){
                    $route['action'] = 'index';
                }
                //получаем префикс для админ контроллеров
                if(!isset($route['prefix'])){
                    $route['prefix'] = '';
                }else{
                    $route['prefix'] .= '\\';
                }

                $route['controller'] = self::upperCamelCase($route['controller']);//контроллер должен быть с заглавной буквы
                //debug($route);
                self::$route = $route; // если совпадение найдено - записывается текущий маршрут
                //debug($route);
                return true;
            }
        }
        return false;
    }

    /**
     * Перенаправляет URL по корректному маршруту
     * @param string $url входящий URL
     * @return void
     */
    public static function dispatch($url){
//        //debug(self::getQueryString($url));
//        $searchStr = self::getQueryString($url);
//
//        if($searchStr){
//            $url = 'search';
//            self::matchRoute($url);
//
//            debug(self::$route);
//
//            //$controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
//            //debug($controller);     // app\controllers\ProductController
//            $controller = 'app\controllers\SearchController';
//            $selfRoute = [
//                'controller' => 'Search',
//                'action'     => 'index',
//                //'alias'      => self::$route['alias'],
//                'alias'      => '',
//                'prefix'     => ''
//            ];
//            //debug($selfRoute);
//
//            if(class_exists($controller)) {
//
//                //$cObj = new $controller(self::$route); //параметры передаем для того, чтобы сформировать нужный нам вид
//                $cObj = new $controller($selfRoute);
//                //$action = self::lowerCamelCase(self::$route['action']) . 'Action';
//                $action = self::lowerCamelCase($selfRoute['action']) . 'Action';
//
//                if (method_exists($cObj, $action)) {
//                    //$searchStr
//                    $cObj->$action();
//                    $cObj->getView();
//                } else {
//                    echo 'Non ok method!' . $controller;
//                }
//            }
//            return;
//        }else{
//            //die('--');
//        }



        /* ***************** */
        $url = self::removeQueryString($url);

        if(self::matchRoute($url)){
           $controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
            //debug(self::$route);
            if(class_exists($controller)){
                $cObj = new $controller(self::$route); //параметры передаем для того, чтобы сформировать нужный нам вид
                $action = self::lowerCamelCase(self::$route['action']) . 'Action';
                //debug(self::$route);
                if(method_exists($cObj, $action)){
                    $cObj->$action();
                    $cObj->getView();
                }else{
                    echo'Non ok method!' . $controller;
                }
            }else{
                echo'Non ok!'. $controller;
            }
        }else{
            http_response_code(404);
            include '404.html';
        }
    }

    protected static function upperCamelCase($name){
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }

    protected static function lowerCamelCase($name){
        return lcfirst(self::upperCamelCase($name));
    }

    protected static function removeQueryString($url){
        if($url){
            $params = explode('&', $url, 2);
            if(false === strpos($params['0'], '=')){
                return trim($params['0'], '/');
            }else{
                return '';
            }
        }
    }

    /* Start ************************* */
    protected static function getQueryString($url){
        if($url) {
            //$q1 = explode('?', 'http://site2.loc/product/phone-gt-c3560/?s=qwerty', 2);
            $q2 = explode('&', $url);
            array_splice($q2, 0, 1);

            if (!isset($q2[0])) {
                //echo('это не поиск строки');
                return false;
            }
            //debug($q2);

            $qq1 = array_map(function ($in) {
                return ['k' => explode('=', $in, 2)[0], 'v' => explode('=', $in, 2)[1]];
            }, $q2);
            //debug($qq1);

            $qq2 = [];
            foreach ($qq1 as $val) {
                $qq2[$val['k']] = $val['v'];
            }
            //debug($qq2);

            if (array_key_exists('s', $qq2)) {
                //echo('это поиск строки "'.$qq2['s'].'"');
                return $qq2['s'];
            }

            return false;
        }
    }
    /* Stop ************************* */

}


