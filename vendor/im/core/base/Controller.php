<?php

namespace im\core\base;


abstract class Controller
{
    /**
     * текущий маршрут и параметры(controller, action, params)
     * @var array
     */
    public $route = [];
    public $prefix;

    /**
     * текущий вид
     * @var string
     */
    public $view;

    /**
     * текущий шаблон
     * @var string
     */
    public $layout;

    /**
     * пользовательские данные
     * @var array
     */
    public $vars = [];

    /**
     * @param $route контроллер и экшн страницы - для подключения вида
     */
    public function __construct($route){
        $this->route = $route;
        $this->view = $route['action'];
        $this->prefix = $route['prefix'];
    }

    public function getView(){
        $vObj = new View($this->route, $this->layout, $this->view);
        $vObj->render($this->vars);
    }

    public function set($vars){
        $this->vars = $vars;
    }

    /**
     * Проверка поступили ли данные асинхронно. Если да - true
     * @return bool
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Загрузка определенного вида при возврате данных AJAX
     * @param $view - вид, который мы передаем в кач-ве параметра
     * @param array $vars
     */
    public function loadView($view, $vars = []){
        extract($vars); //извлекаем переменные из массива
        require APP . "/views/{$this->prefix}{$this->route['controller']}/{$view}.php";
        die;
    }


}