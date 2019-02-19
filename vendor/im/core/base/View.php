<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 01.11.2018
 * Time: 11:55
 */

namespace im\core\base;


class View
{
    /**
     * текущий маршрут и параметры(controller, action, params)
     * @var array
     */
    public $route = [];

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
     * @var array массив с мета-данными
     */
    public static $meta = ['title' => '', 'desc' => '', 'keywords' => ''];

    /**
     * переменная, в которой будут храниться вырезанные скрипты
     * @var array
     */
    public $scripts = [];

    public function __construct($route, $layout = '', $view = ''){
        //var_dump($layout);
        //var_dump($view);

        $this->route = $route;
        if($layout === false){
            $this->layout = false;
        }else{
            $this->layout = $layout ?: LAYOUT;
        }
        $this->view = $view;
    }

    public function render($vars){
        if(is_array($vars))extract($vars);
        /**
         * указываем путь к текущему виду
         */
        $file_view = APP . "/views/{$this->route['prefix']}{$this->route['controller']}/{$this->view}.php";
        $file_view = str_replace('\\', '/',$file_view);
        //debug($file_view, 'file_view 2');
        //var_dump(is_file($file_view));
        ob_start();
        if(is_file($file_view)){
            require $file_view;
        }else{
            echo "<p>Не найден вид <b>{$file_view}</b></p>";
        }
        $content = ob_get_clean();

        /**
         * Подключение шаблона
         */
        if(false !== $this->layout){
            $file_layout = APP . "/views/layouts/{$this->layout}.php";
            if(is_file($file_layout)){
                $content = $this->getScript($content);//обрабатываем контент, удаляя возможные скрипты
                /**
                 * Механизм вставки скриптов после подключения jquery и пр, указанных в шаблоне
                 * Затем идем в шаблон и после подключения jquery ставим метку для включения наших скриптов
                 * см. шаблон default.php
                 */
                $scripts = [];
                if(!empty($this->scripts)){
                    $scripts = $this->scripts[0];
                }
                require $file_layout;
            }else{
                echo "<p>Не найден шаблон <b>{$file_layout}</b></p>";
            }
        }
    }

    /**
     * Метод ищет скрипты. Если они есть - вырезаются и контент возвращается без них, если нет - контент отдается в том виде, что есть.
     * @param $content
     * @return mixed
     */
    protected function getScript($content){
        $pattern = "#<script.*?>.*?</script>#si";
        preg_match_all($pattern, $content, $this->scripts);//функция будет искать скрипты, если найдет - положит в св-во scripts
        //если в рез-те массив не пуст - берем паттерн, вырезает скрипты, заменяя их пустой строкой в переменной content
        if(!empty($this->scripts)){
            $content = preg_replace($pattern, '', $content);
        }
        return $content;
    }

    public static function getMeta(){
        echo '<title>' . self::$meta['title'] . '</title>
        <meta name="description" content="'. self::$meta['desc'] .'">
        <meta name="keywords" content="'. self::$meta['keywords'] .'">';

    }
    public static function setMeta($title = '', $desc = '', $keywords = ''){
        self::$meta['title'] = $title;
        self::$meta['desc'] = $desc;
        self::$meta['keywords'] = $keywords;
    }

    /**
     * Метод для включения части кода в вид
     * Принимает на вход путь к файлу из папки views, который нужно подключить
     * вызываем метод из нужного вида, передаем туда что-то типа <?php getPart('inc/sidebar');?>
     * где inc - созданная нами папка для частей кода и sidebar.php - файл с кодом
     * @param $file
     */
    public function getPart($file){
        $file = APP . "/views/{$file}.php";
        if(is_file($file)){
            require_once $file;
        }else{
            echo "File {$file} not found...";
        }
    }
}