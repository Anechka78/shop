<?php
error_reporting(-1);

use im\core\Router;

$query = rtrim($_SERVER['QUERY_STRING'], '/'); //строка-запрос от пользователя


define('WWW', __DIR__);
define('CORE', dirname(__DIR__).'/vendor/im/core');
define('ROOT', dirname(__DIR__));
define('APP', dirname(__DIR__).'/app');
define('CONF', dirname(__DIR__).'/config');
define('LIBS', dirname(__DIR__).'/vendor/im/libs');
define('CACHE', dirname(__DIR__).'/tmp/cache');
define('LAYOUT', 'default'); //шаблон по умолчанию

// http://ishop2.loc/public/index.php
$app_path = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
// http://ishop2.loc/public/
$app_path = preg_replace("#[^/]+$#", '', $app_path);
// http://ishop2.loc
$app_path = str_replace('/public/', '', $app_path);
define("PATH", $app_path);
define("ADMIN", PATH.'/admin');

require '../vendor/im/libs/functions.php';
require __DIR__. '/../vendor/autoload.php';

/*spl_autoload_register(function($class){
    $file = ROOT.'/'.str_replace('\\', '/', $class).'.php';

    if(is_file($file)){
        require_once $file;
    }
});*/

new \im\core\App;//класс для кеширования
//var_dump(\im\core\App::$app->getProperties());


//debug($query);
//debug($_SERVER['REQUEST_URI']);
Router::add('^page/(?P<action>[a-z-]+)/(?P<alias>[a-z-]+)$', ['controller' => 'Page']);
Router::add('^page/(?P<alias>[a-z-]+)$', ['controller' => 'Page', 'action' => 'view']);
Router::add('^category/(?P<alias>[a-zA-Z0-9-]+)/?$', ['controller' => 'Category', 'action' => 'index']);
Router::add('^product/(?P<alias>[a-zA-Z0-9-]+)/?$', ['controller' => 'Product', 'action' => 'view']);


//Админская часть роутинга
Router::add('^admin$', ['controller' => 'User', 'action' => 'index', 'prefix' => 'admin']);
Router::add('^admin$', ['controller' => 'Main', 'action' => 'index', 'prefix' => 'admin']);
Router::add('^admin/?(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$', ['prefix' => 'admin']);
//Первое дефолтное правило совпадает с пустой строкой, когда отрабатывает контроллер и экшн главной страницы
Router::add('^$', ['controller' => 'Main', 'action' => 'index']);
//Первое дефолтное правило разрешаем в контроллере и экшне латиницу и знак тире (+ означает один и более символов)
//Ловить кнтр и экшн помогают группирующие скобки (). Кнтр и экшн массив не нужен, тк в первом совпадении у нас будет кнтр, а во втором - экшн
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

Router::dispatch($query);

// test
