<?php
namespace app\controllers;
use app\models\Main;
use im\core\App;
use im\core\base\View;
use im\core\Regisrty;
use im\libs\Pagination;
use PHPMailer\PHPMailer\PHPMailer;


class MainController extends AppController
{
    //public $layout = 'main';//переопределили шаблон для всех экшнов

    public function indexAction(){
        //$this->layout = false;
        //$this->layout = 'main';//переопределили шаблон на уровне экшна
        //$this->view = 'test'; //переопределили вид на уровне экшна
        //App::$app->getList();

        //$title = 'PAGE TITLE';

        $model = new Main;

        View::setMeta('Главная страница', 'Описание страницы', 'Ключевые слова');

        //$mailer = new PHPMailer();
        //var_dump($mailer);


        /*$posts = App::$app->cache->get('posts'); //кеширование
        if(!$posts){
            $posts = $model->findAll();
            App::$app->cache->set('posts', $posts);//кешируем посты на сутки по дефолту, можно третьим параметром указать другой срок кеширования
        }*/

        $products = $model->findLast(8);
        //debug($products);
        /*
         * ПАГИНАЦИЯ
         */
//        $total = $model->selectCount();
//        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
//        $perpage = 4;
//
//        $pagination = new Pagination($page, $perpage, $total);
//        $start = $pagination->getStart();
        /*
         * ПАГИНАЦИЯ - КОНЕЦ
         */

        //$posts = $model->findAllLimit('news', (int) $start, (int) $perpage);
        $rsHits = $model->findHits('4');
        //debug($rsHits);

        /*App::$app->cache->set('posts', $posts);//кешируем посты на сутки по дефолту, можно третьим параметром указать другой срок кеширования
        */


        //$register = Regisrty::instance();
        //$register->getList();
        //$register->test->go();
        //debug($posts);

        //$post = $model->findOne(5);//здесь можем переопределить столбец выборки, сделав его не id как по умолчанию, а другим, см Model.php
        //debug($post);

        //$data = $model->findBySql("SELECT * FROM {$model->table} WHERE title LIKE ?", ['%77%']);
        //debug($data);

        //$data1 = $model->findLike('77', 'title');
        //debug($data1);

        //$data = $model->queryFetch();
        //debug($data);

        //Подключаем передачу мета-данных для страницы. Получили из БД данные о странице, затем вывели их, записали в массив мета и передаем
        // в вид через compact в виде массива 'meta'. Все это из Апп-контроллера
        //$this->setMeta($posts->title, $posts->description, $posts->keyword);
        //$meta = $this->meta;


        $this->set(compact('posts', 'pagination', 'total', 'products', 'rsHits'));
    }

    public function testAction(){
        /**
         * Проверям метод на Аякс, прописанный в Controller.php
         */
        if($this->isAjax()){
            $model = new Main();

            $post = $model->findOne($_POST['id'], 'id');
            //debug($post);
            //debug($this->route);
            //вызываем метод loadView из Controller.php, чтобы загрузить данные и передать их в вид
            $this->loadView('_test', compact('post'));
            die;
        }
        echo 222;
        //$this->layout = 'test';
        $title = 'TEST PAGE TITLE';
    }
}