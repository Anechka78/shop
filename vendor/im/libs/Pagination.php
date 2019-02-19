<?php

namespace im\libs;


class Pagination
{
    public $currentPage;       //текущая страница
    public $perpage;           //количество записей на странице
    public $total;             // общее количество записей
    public $countPages;        // общее количество страниц
    public $uri;               // базовый адрес, к которому мы добавляем пагинацию

    /**
     * @param $page int текущей страницы
     * @param $perpage int кол-во записей на странице
     * @param $total int общее количество записей
     */
    public function __construct($page, $perpage, $total){
        $this->perpage = $perpage;
        $this->total = $total;
        $this->countPages = $this->getCountPages();
        $this->currentPage = $this->getCurrentPage($page);
        $this->uri = $this->getParams();
    }

    public function getHtml(){
        $back = null; // ссылка НАЗАД
        $forward = null; // ссылка ВПЕРЕД
        $startpage = null; // ссылка В НАЧАЛО
        $endpage = null; // ссылка В КОНЕЦ
        $page2left = null; // вторая страница слева
        $page1left = null; // первая страница слева
        $page2right = null; // вторая страница справа
        $page1right = null; // первая страница справа

        if( $this->currentPage > 1 ){
            $back = "<li><a class='nav-link' href='{$this->uri}page=" .($this->currentPage - 1). "'>&lt;</a></li>";
        }

        if( $this->currentPage < $this->countPages ){
            $forward = "<li><a class='nav-link' href='{$this->uri}page=" .($this->currentPage + 1). "'>&gt;</a></li>";
        }

        if( $this->currentPage > 3 ){
            $startpage = "<li><a class='nav-link' href='{$this->uri}page=1'>&laquo;</a></li>";
        }
        if( $this->currentPage < ($this->countPages - 2) ){
            $endpage = "<li><a class='nav-link' href='{$this->uri}page={$this->countPages}'>&raquo;</a></li>";
        }
        if( $this->currentPage - 2 > 0 ){
            $page2left = "<li><a class='nav-link' href='{$this->uri}page=" .($this->currentPage-2). "'>" .($this->currentPage - 2). "</a></li>";
        }
        if( $this->currentPage - 1 > 0 ){
            $page1left = "<li><a class='nav-link' href='{$this->uri}page=" .($this->currentPage-1). "'>" .($this->currentPage-1). "</a></li>";
        }
        if( $this->currentPage + 1 <= $this->countPages ){
            $page1right = "<li><a class='nav-link' href='{$this->uri}page=" .($this->currentPage + 1). "'>" .($this->currentPage+1). "</a></li>";
        }
        if( $this->currentPage + 2 <= $this->countPages ){
            $page2right = "<li><a class='nav-link' href='{$this->uri}page=" .($this->currentPage + 2). "'>" .($this->currentPage + 2). "</a></li>";
        }

        return '<ul class="pagination">' . $startpage.$back.$page2left.$page1left.'<li class="active"><a>'.$this->currentPage.'</a></li>'.$page1right.$page2right.$forward.$endpage . '</ul>';
    }

    /**
     * Приводит объект к строке, в нашем случае вернет готовый html код
     * @return mixed
     */
    public function __toString(){
        return $this->getHtml();
    }

    /**
     * Возвращает общее количество страниц
     * @return int
     */
    public function getCountPages(){
        return ceil($this->total / $this->perpage) ?: 1;
    }

    /**
     * Взять текущую страницу
     * @param $page берем из массива GET и обрабатываем
     * @return int
     */
    public function getCurrentPage($page){
        if(!$page || $page < 1) $page = 1;
        if($page > $this->countPages) $page = $this->countPages;
        return $page;
    }

    /**
     * С какой записи нам надо начинать выборку данных из БД
     * @return int
     */
    public function getStart(){
        return ($this->currentPage - 1)* $this->perpage;
    }

    /**
     *
     * @return string
     */
    public function getParams(){
        $url = $_SERVER['REQUEST_URI']; // распарсиваем строку запроса, который идет с параметрами типа ?page=2&lang=en
        $url = explode('?', $url);//делим по вопросительному знаку
        $uri = $url[0] . '?';
        if(isset($url[1]) && $url[1] != ''){ //если есть какие-то GET-параметры в запросе
            $params = explode('&', $url[1]); //разбиваем их по амперсанту, чтобы получить пары ключ-значение(page=2)
            foreach($params as $param){ // перебираем их в цикле
                // если это не page=, нам нужен параметр и мы его добавляем в $uri, те все, за исключением page=, его вырезаем и слепляем строку
                if(!preg_match("#page=#", $param)) $uri .= "{$param}&amp;";
            }
        }
        return urldecode($uri);
    }
}