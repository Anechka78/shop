<?php

namespace app\controllers\admin;

use app\models\admin\Model;
use app\models\admin\Product;
use im\core\App;
use im\core\base\View;
use im\libs\Pagination;

class ProductController extends AppController{

    public function indexAction(){
        //$data = $_POST;
        //debug($data);

        $model = new Product();
        $page = isset($_GET['page'])? (int)$_GET['page'] : 1;

        if(!empty($_POST)){
            $val = $_POST['val'];
            $sort = $_POST['sort'];
        } elseif(!empty($_SESSION['products_sort'])){
            $val = $_SESSION['products_sort']['val'];
            $sort = $_SESSION['products_sort']['sort'];
        }else{
            $val = 'id';
            $sort = 'DESC';
        }

        $perpage = 5;
        $count = $model->selectCount('products');
        $pagination = new Pagination($page, $perpage, $count);

        $start = $pagination->getStart();

        $products = $model->getAllProducts($start, $perpage, $val, $sort);
        $_SESSION['products_sort']['val'] = $val;
        $_SESSION['products_sort']['sort'] = $sort;

        View::setMeta('Список товаров', '', '');
        $this->set(compact('products', 'pagination', 'count'));
    }

    /**
     * Добавление нового товара
     */
    public function addAction(){
        View::setMeta('Новый товар', '', '');
        $product = new Product();
        $vendors = $product->getAllVendors();
        $mods = $product->getAllMods();
        $this->set(compact('vendors', 'mods'));
        if(!empty($_POST)){
            $data = $_POST;

            //проверка на заполнение кол-ва товара
            if($data['count'] == '0' || $data['count'] == '') {
                if (isset($_SESSION['pd'])) {
                    foreach ($_SESSION['pd'] as $k => $v) {
                        if ($v['count'] == '0' || $v['count'] == '') {
                            $_SESSION['error'] = 'Не указано количество товара';
                            redirect();
                        }
                    }
                } elseif(!isset($_SESSION['pd']) && isset($_SESSION['pv'])){
                    foreach ($_SESSION['pv'] as $k => $v) {
                        if ($v['count'] == '0' || $v['count'] == '') {
                            $_SESSION['error'] = 'Не указано количество товара';
                            redirect();
                        }
                    }
                }elseif(!isset($_SESSION['pd']) && !isset($_SESSION['pv'])){
                    $_SESSION['error'] = 'Не указано количество товара';
                    redirect();
                }

            }
            //проверка на заполнение цены товара
            if($data['price'] == '0' || $data['price'] == '') {
                if (isset($_SESSION['pd'])) {
                    foreach ($_SESSION['pd'] as $k => $v) {
                        if ($v['price'] == '0' || $v['price'] == '') {
                            $_SESSION['error'] = 'Не указана цена товара';
                            redirect();
                        }
                    }
                } elseif(!isset($_SESSION['pd']) && isset($_SESSION['pv'])) {
                    foreach ($_SESSION['pv'] as $k => $v) {
                        if ($v['price'] == '0' || $v['price'] == '') {
                            $_SESSION['error'] = 'Не указана цена товара';
                            redirect();
                        }
                    }
                }elseif(!isset($_SESSION['pd']) && !isset($_SESSION['pv'])){
                    $_SESSION['error'] = 'Не указана цена товара';
                    redirect();
                }

            }

            //проверка на заполнение веса товара
            if($data['weight'] == '0' || $data['weight'] == '') {
                if (isset($_SESSION['pd'])) {
                    foreach ($_SESSION['pd'] as $k => $v) {
                        if ($v['weight'] == '0' || $v['weight'] == '') {
                            $_SESSION['error'] = 'Не указан вес товара';
                            redirect();
                        }
                    }
                } elseif(!isset($_SESSION['pd']) && isset($_SESSION['pv'])) {
                    foreach ($_SESSION['pv'] as $k => $v) {
                        if ($v['weight'] == '0' || $v['weight'] == '') {
                            $_SESSION['error'] = 'Не указан вес товара';
                            redirect();
                        }
                    }
                }elseif(!isset($_SESSION['pd']) && !isset($_SESSION['pv'])){
                    $_SESSION['error'] = 'Не указан вес товара';
                    redirect();
                }

            }

            //валидация данных, пришедших в POST
            $product->load($data);
            $product->attributes['status'] = $product->attributes['status'] ? '1' : '0';
            $product->attributes['hit'] = $product->attributes['hit'] ? '1' : '0';

            if(isset($_SESSION['single'])){
                $data['image'] = $_SESSION['single'];
            }

            if(!$product->validate($data)){
                $product->getErrors();
                $_SESSION['form_data'] = $data;
                redirect();
            }
            if(isset($data['related'])){
                $related = $data['related'];
                unset($data['related']);
            }

            $data_product = [];
            $data_product['name'] = $data['name'];
            $data_product['category_id'] = $data['category_id'];
            $data_product['vendor'] = $data['vendor'];
            if(isset($data['image'])){
                $data_product['image'] = $data['image'];
            }
            $data_product['title'] = $data['title'];
            $data_product['meta_desc'] = $data['meta_desc'];
            $data_product['description'] = $data['description'];
            $data_product['price'] = $data['price'];
            $data_product['old_price'] = $data['old_price'];
            $data_product['count'] = $data['count'];
            $data_product['weight'] = $data['weight'];
            if(isset($data['status'])){
                $data_product['status'] = '1';
            }
            if(isset($data['hit'])){
                $data_product['hit'] = '1';
            }

            if($id = $product->insertAndReturnId('products', $data_product)){
                $alias = [];
                $model = new Model();
                $alias['alias'] = $model->createAlias('products', 'alias', $data['name'], $id);
                $model->updateTable('products', $alias, 'id', $id);
                if(isset($related)){
                    $product->editRelatedProducts($id, $related);
                }
                $product->saveGallery($id);
                $product->addProductDependenciesInDb($id);
                $product->addProductValuesInDb($id);

                $_SESSION['success'] = 'Товар добавлен';
            }
            redirect();
        }
    }

    public function relatedProductAction(){
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $data['items'] = [];
        $model = new Product();
        $products = $model->findLike($q, 'name' ,'products');
        if($products){
            $i = 0;
            foreach($products as $k=>$v){
                $data['items'][$i]['id'] = $v['id'];
                $data['items'][$i]['text'] = $v['name'];
                $i++;
            }
        }
        echo json_encode($data);
        die;
    }

    public function addImageAction(){
        if(isset($_GET['upload'])){
            if($_POST['name'] == 'multi'){
                $wpmax = App::$app->getProperty('img_width');
                $hpmax = App::$app->getProperty('img_height');
                $wmax = App::$app->getProperty('gallery_width');
                $hmax = App::$app->getProperty('gallery_height');
            }
            $name = $_POST['name'];
            $product = new Product();
            $product->uploadImg($name, $wpmax, $hpmax, $wmax, $hmax);
        }
    }

    /**
     * Получаем значения характеристики при ее выборе пользователем
     */
    public function getModsAction(){
        $id = $_POST['id'];
        $model = new Product();
        $mod_values = $model->getModValues($id);
        $resData = array();
        if(!$mod_values){
            $resData['error'] = 1;
            $resData['message'] = 'Выборки нет, повторите попытку позже';
        }else{
            $resData['success'] = 1; // записываем в результирующие данные успех операции
            $resData['message'] = 'Значения характеристики получены';
            $resData['mod_values'] = $mod_values;
        }
        die(json_encode($resData));
    }

    /**
     * Добавляем в сессию простые характеристики
     */
    public function setPropertyValueAction(){
        $pv = [];
        $pv['property_name'] = $_POST['property_name_name'];
        $pv['property_value'] = $_POST['property_name_value'];
        $pv['property_name_value'] = (int)$_POST['data']['prop_name'];
        $pv['property_value_value'] = (int)$_POST['data']['prop_value'];
        $pv['count'] = isset($_POST['data']['pv_count']) ? (int)$_POST['data']['pv_count'] : null;
        $pv['price'] = isset($_POST['data']['pv_price']) ? (floatval($_POST['data']['pv_price'])) : null;
        $pv['old_price'] = isset($_POST['data']['pv_oldprice']) ? (floatval($_POST['data']['pv_oldprice'])) : null;
        $pv['weight'] = isset($_POST['data']['pv_weight']) ? (floatval($_POST['data']['pv_weight'])) : null;

        $resData = [];

        if($pv['property_name_value'] == 0 || $pv['property_value_value'] == 0){
            $resData['error'] = 1;
            $resData['message'] = 'Не выбраны характеристики';
            die(json_encode($resData));
        }

        if(isset($_SESSION['pv'])){
            foreach($_SESSION['pv'] as $k=>$v){
                if($v['property_name'] == $pv['property_name'] &&
                    $v['property_value'] == $pv['property_value']){
                    $resData['error'] = 1;
                    $resData['message'] = 'Такие характеристики уже есть для этого товара';
                    die(json_encode($resData));
                }
            }
        }

        $_SESSION['pv'][] = $pv;
        $key = max(array_keys($_SESSION['pv']));
        $resData['success'] = 1; // записываем в результирующие данные успех операции
        $resData['message'] = 'Характеристика успешно добавлена';
        $resData['pv'] = $pv;
        $resData['key'] = $key;
        die(json_encode($resData));
    }

    /**
     * Добавляем в сессию взаимозависимые характеристики
     */
    public function setPropertyDependenceAction(){
        $data = $_POST['data'];
        $pd_val = [];
        $pd_val['parent_name_value'] = $_POST['parent_name_value'];
        $pd_val['parent_names_value'] = $_POST['parent_names_value'];
        $pd_val['child_name_value'] = $_POST['child_name_value'];
        $pd_val['child_names_value'] = $_POST['child_names_value'];

        $pd = [];
        $pd['parent_property_name'] = isset($data['parent_name'])? (int)$data['parent_name'] : null;
        $pd['parent_property_names'] = isset($data['parent_names'])? (int)$data['parent_names'] : null;
        $pd['child_property_name'] = isset($data['child_name'])? (int)$data['child_name'] : null;
        $pd['child_property_names'] = isset($data['child_names'])? (int)$data['child_names'] : null;
        $pd['count'] = isset($data['pd_count'])? (int)$data['pd_count'] : null;
        $pd['price'] = isset($data['pd_price'])? (floatval($data['pd_price'])) : null;
        $pd['old_price'] = isset($data['pd_oldprice'])? (floatval($data['pd_oldprice'])) : null;
        $pd['weight'] = isset($data['pd_weight'])? (floatval($data['pd_weight'])) : null;

        $resData = array();
        if($pd['parent_property_name'] == 0 || $pd['parent_property_names'] == 0 || $pd['child_property_name'] == 0 || $pd['child_property_names'] == 0){
            $resData['error'] = 1;
            $resData['message'] = 'Не выбраны характеристики';
            die(json_encode($resData));
        }

        if($pd['count'] !== '0' && !empty($pd['count'])){
            $resData['count'] = 1;
        }

        if(isset($_SESSION['pd'])){
            foreach($_SESSION['pd'] as $k=>$v){
                if($v['parent_property_name'] == $pd['parent_property_name'] &&
                   $v['parent_property_names'] == $pd['parent_property_names'] &&
                   $v['child_property_name']  == $pd['child_property_name'] &&
                   $v['child_property_names']  == $pd['child_property_names']){
                    $resData['error'] = 1;
                    $resData['message'] = 'Такие характеристики уже есть для этого товара';
                    die(json_encode($resData));
                }
            }
        }
        $_SESSION['pd'][] = $pd;
        //$key = array_keys($_SESSION['pd'], max($_SESSION['pd']))[0];
        $key = max(array_keys($_SESSION['pd']));

        $resData['success'] = 1; // записываем в результирующие данные успех операции
        $resData['message'] = 'Характеристика успешно добавлена';
        $resData['pd_val'] = $pd_val;
        $resData['pd'] = $pd;
        $resData['key'] = $key;
        die(json_encode($resData));
    }

    /**
     * Удаление характеристики из сессионного массива
     */
    public function deleteModAction(){
        $id = (int)$_POST['id'];
        $name = $_POST['name'];

        $resData = array();
        if(!$id && !$name){
            $resData['error'] = 1;
            $resData['message'] = 'Нет характеристики для удаления';
            die(json_encode($resData));
        }
        unset($_SESSION[$name][$id]);

        $resData['success'] = 1; // записываем в результирующие данные успех операции
        $resData['message'] = 'Характеристика успешно удалена';
        $resData['id'] = $id;
        $resData['name'] = $name.'-';
        die(json_encode($resData));
    }





}