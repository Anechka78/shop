<?php

namespace app\controllers;

use app\models\Breadcrumbs;
use app\models\Category;
use im\core\App;
use im\core\base\View;
use im\core\Regisrty;
use im\libs\Pagination;
use im\libs\Timer;

class CategoryController extends AppController
{
public function indexAction(){
    //var_dump($this->route);
    Timer::start();
    $id = $this->route['alias'];
    if($id == null) redirect('/');

    $model = new Category();
    $category = $model->findOne($id, $field = 'alias')[0];//здесь можем переопределить столбец выборки, сделав его не id как по умолчанию, а другим, см Model.php

    $breadcrumbs = Breadcrumbs::getBreadcrumbs($category['id'], 'alias');
    //Если вдруг обращаться не через массив cats, а через БД
    //$arr = []; //массив для будущих id категорий, по которым отбираются товары
    /*$rsChildCats = $model->findEqual($category['id'], 'parent_id'); //находим дочерние категории

    $arr[] = $id;
    if($rsChildCats){
        foreach($rsChildCats as $key => $cat){
            $arr[] = $cat['id'];
        }
    }*/
    $rsChildCats = rtrim($model->getIds($category['id']), ',');

    $rsChildCats = !$rsChildCats ? $category['id'] : $category['id'].','.$rsChildCats;
    //debug($rsChildCats); die();

    $title = $category['title'];
    $description = $category['description'];
    //$keywords = $category['keywords']; раскомментировать, если понадобится, в целом - устарело
    View::setMeta("Страница категории {$title}", $description, 'Ключевые слова для категории');

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perpage = App::$app->getProperty('pagination');

    $allproducts = $model->findProducts('products', $rsChildCats);

    $total = count($allproducts);
    $pagination = new Pagination($page, $perpage, $total);
    $start = $pagination->getStart();

    $products = array_slice($allproducts, $start, $perpage);;

    $this->set(compact('category', 'products', 'breadcrumbs', 'pagination', 'total'));
    echo Timer::finish() . ' сек.';
    }
}