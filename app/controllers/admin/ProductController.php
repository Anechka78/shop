<?php

namespace app\controllers\admin;

use app\models\admin\Model;
use app\models\admin\Product;
use im\core\App;
use im\core\base\View;
use im\libs\Pagination;

class ProductController extends AppController{

    public function indexAction(){
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

        $perpage = 7;
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
        //debug($_SESSION);
        View::setMeta('Добавить товар', '', '');
        $product = new Product();
        $vendors = $product->getAllVendors();
        $mods = $product->getAllMods();
        $secret_key = 515556;
//        $secret_key = rand(100000, 999999);
//
//        $_SESSION['add_product'][$secret_key] = [];

        $this->set(compact('vendors', 'mods', 'secret_key'));
    }

    /**
     * Добавление основной информации о товаре
     */
    public function addMainInfoAction(){
        $resData=[];
        if(!empty($_POST)){
            $data = $_POST;
            //пока кладем пустую картинку
            $data['image'] = 'no-image.jpg';
            $secret_key = $data['secret_key'];

            $_SESSION['form_data'] = $data;
            $_SESSION['add_product'][$secret_key] = $data;

            $product = new Product();

            //проверка на заполнение цены и веса товара
            if(!$product->checkProductParams($data['price'], 'price')){
                $resData['success'] = 0;
                $resData['message'] = 'Базовая цена товара должна быть указана';
                die(json_encode($resData));
            }
            if(!$product->checkProductParams($data['weight'], 'weight')){
                $resData['success'] = 0;
                $resData['message'] = 'Базовый вес товара должен быть указан';
                die(json_encode($resData));
            }
            if(empty($data['count'])){
                $data['count'] = 0;
            }
            if(empty($data['old_price'])){
                $data['old_price'] = 0;
            }
            //валидация данных, пришедших в POST
            $product->load($data);
            if(!$product->validate($data)){
                $resData['success'] = 0;
                $resData['message'] = $product->getErrors();
                die(json_encode($resData));
            }

            //добавление товара в БД таблицу products
            if($id = $product->insertAndReturnId('products', $data)){
                $alias = [];
                $model = new Model();
                $alias['alias'] = $model->createAlias('products', 'alias', $data['name'], $id);
                $model->updateTable('products', $alias, 'id', $id);
                $data['id'] = $id;
                $data['alias'] = $alias['alias'];
                $_SESSION['add_product'][$secret_key]['id'] = $id;
                $_SESSION['add_product'][$secret_key]['alias'] = $alias['alias'];

                $resData['secret_key'] = $secret_key;
                $resData['product_id'] = $id;
                $resData['product_name'] = $data['name'];
                $resData['success'] = 1;
                $resData['message'] = 'Информация о товаре была добавлена';
            }else{
                $resData['success'] = 0;
                $resData['message'] = 'Сбой при добавлении информации';
            }
        }else{
            $resData['success'] = 0;
            $resData['message'] = 'Информация не была добавлена';
        }
        die(json_encode($resData));
    }

    /**
     * Обновление основной информации о товаре
     */
    public function updateMainInfoAction(){
        $resData=[];

        if(!empty($_POST)){
            $data = $_POST;
        }else{
            $resData['success'] = 0;
            $resData['message'] = 'Нет данных для обновления';
            die(json_encode($resData));
        }

        $id = $data['product_id'];
        unset($data['product_id']);

        if(isset($data['secret_key'])){
            $secret_key = $data['secret_key'];
            unset($data['secret_key']);
        }

        $model = new Product();
        $product = $model->findOne($id, 'id', 'products')[0];

        $productDiff = array_diff_assoc($data, $product);
        //debug($productDiff); die();

        if(!empty($productDiff)){
            if(!$model->updateTable('products', $productDiff, 'id', $id)){
                $resData['success'] = 0;
                $resData['message'] = 'Обновить данные не удалось';
            }else{
                if(!empty($productDiff['name'])){
                    $alias = [];
                    $aliasModel = new Model();
                    $alias['alias'] = $aliasModel->createAlias('products', 'alias', $data['name'], $id);
                    $model->updateTable('products', $alias, 'id', $id);
                }
                $resData['product_name'] = $data['name'];
                $resData['success'] = 1;
                $resData['message'] = 'Информация о товаре была обновлена';
                if($secret_key){
                    foreach($_SESSION['add_product'][$secret_key] as $key=>$value){
                        foreach($productDiff as $key1=>$value1){
                            if($key == $key1){
                                $_SESSION['add_product'][$secret_key][$key] = $value1;
                            }
                        }
                    }
                }
            }
        }else{
            $resData['success'] = 0;
            $resData['message'] = 'Нет данных для обновления';
        }
        die(json_encode($resData));
    }


    /**
     * Добавление связанных товаров
     */
    public function addRelatedAction(){
        if(!empty($_POST)){
            $data = $_POST;
            $resData = [];
            if(empty($data['product_id'])){
                $resData['success'] = 0;
                $resData['message'] = 'Нет информации о товаре для ввода';
                die(json_encode($resData));
            }
            $product = new Product();
            $product->addRelatedProducts($data['product_id'], $data['related']);
            $_SESSION[$data['secret_key']]['related'] = $data['related'];
            $resData['success'] = 1;
            $resData['message'] = 'Информация о связанных товарах была добавлена';
            die(json_encode($resData));
        }
        $resData['success'] = 0;
        $resData['message'] = 'Нет информации';
        die(json_encode($resData));
    }














    public function figAction(){

        if(!empty($_POST)){
            $data = $_POST;
            $_SESSION['form_data'] = $data;

            foreach ($data as $k=>$v){
                if(empty($v)){
                    $data[$k] =0;
                }
            }
            $product = new Product();
            //проверка на заполнение кол-ва товара, цены и веса
            $product->checkProductParams($data['count'], 'count', 'Не указано количество товара');
            $product->checkProductParams($data['price'], 'price', 'Не указана цена товара');
            $product->checkProductParams($data['weight'], 'weight', 'Не указан вес товара');

            //валидация данных, пришедших в POST
            $product->load($data);

            $product->attributes['status'] = $product->attributes['status'] ? '1' : '0';
            $product->attributes['hit'] = $product->attributes['hit'] ? '1' : '0';

            if(isset($_SESSION['single'])){
                $data['image'] = $_SESSION['single'];
            }

            if(!$product->validate($data)){
                $product->getErrors();
                redirect();
            }
            if(!isset($data['image'])){
                $data['image'] = 'no-image.jpg';
            }
            if(isset($data['status'])){
                $data['status'] = '1';
            }else{
                $data['status'] = '0';
            }
            if(!isset($data['hit'])){
                $data['hit'] = '0';
            }

            if($id = $product->insertAndReturnId('products', $data)){
                $alias = [];
                $model = new Model();
                $alias['alias'] = $model->createAlias('products', 'alias', $data['name'], $id);
                $model->updateTable('products', $alias, 'id', $id);
                $data['id'] = $id;
                $data['alias'] = $alias['alias'];

                if(isset($data['related'])){
                    $product->editRelatedProducts($id, $data['related']);
                }
                $product->saveGallery($id);
                $product->addProductDependenciesInDb($id, $data['category_id']);
                $product->addProductValuesInDb($id, $data['category_id']);

                //Заносим id товара, название и описание в таблицу products_search для будущего поиска - нужно на этапе добавления нового товара!!!!
                $product->setProductToSearch($id, $data['name'], $data['description']);

                $model_product = new \app\models\Product();
                //характеристики товара
                $product_properties = $model_product->getProductPropertiesByIds($id);
                $propertiesArr = [];
                foreach($product_properties as $property){
                    $propertiesArr[$property['p_name']][$property['id']]['product_id']     =  $property['product_id'];
                    $propertiesArr[$property['p_name']][$property['id']]['pv_name']        =  $property['pv_name'];
                    $propertiesArr[$property['p_name']][$property['id']]['pv_value']       =  $property['pv_value'];
                    $propertiesArr[$property['p_name']][$property['id']]['price']          =  $property['price'];
                    $propertiesArr[$property['p_name']][$property['id']]['old_price']      =  $property['old_price'];
                    $propertiesArr[$property['p_name']][$property['id']]['count']          =  $property['count'];
                    $propertiesArr[$property['p_name']][$property['id']]['weight']         =  $property['weight'];
                }
                $data['propertiesArr'] = $propertiesArr;

                //Зависимые характеристики товара
                $mods = $model_product->getProductDependenciesById(null, $id);

                foreach($mods as $k=>$v){
                    $parent_name = $v['p_name'];
                    $child_name = $v['ch_name'];
                }

                $data['parent_name'] = $parent_name;
                $data['child_name'] = $child_name;

                $parentArr = [];
                foreach($mods as $myRow){
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['product_id']       =  $myRow['product_id'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['parent_name']      =  $myRow['p_name'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['child_name']       =  $myRow['ch_name'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['child_val']        =  $myRow['ch_val'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['child_value']      =  $myRow['ch_value'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['count']            =  $myRow['count'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['weight']           =  $myRow['weight'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['price']            =  $myRow['price'];
                    $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['old_price']        =  $myRow['old_price'];
                }

                $childsArr = [];
                foreach ($parentArr as $size => $colors){
                    foreach($colors as $key => $value){
                        $childsArr[$value['child_val']][$value['child_value']][$key] = $size;
                    }
                }
                $data['parentArr'] = $parentArr;
                $data['childsArr'] = $childsArr;

                //галерея из фотографий
                $gallery = $model_product->getProductImages($id);
                $data['gallery'] = $gallery;

                $data = json_encode($data);
                $product->jsonproduct($id, $alias['alias'], $data);

                $_SESSION['success'] = 'Товар добавлен';
                unset($_SESSION['form_data']);
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

    /**
     * Редактирование товара
     */
    public function editAction(){
        if(!empty($_POST)){

        }
        $id = $this->getRequestID();

        $ad_model = new \app\models\admin\Product();
        //$product = $model->getProductByAlias($alias);
        $res = $ad_model->getProductByJson('product_id', $id);

        $product = json_decode($res['product_json'], true);
        //debug($res); die();
        $product['cat_name']= $res['cat_name'];
        $product['cat_alias']= $res['cat_alias'];
        $product['vn_name']= $res['vn_name'];
        //debug($jsproduct); die();



        App::$app->setProperty('parent_id', $product['category_id']);
        $obg = new Product();
        $vendors = $obg->getAllVendors();

        $model = new \app\models\Product();

//        $product = $model->getProductInfoById($id);
//        //debug($product);
//        App::$app->setProperty('parent_id', $product['category_id']);
//        $obg = new Product();
//        $vendors = $obg->getAllVendors();
//
//        //связанные товары
//        $product['related'] = $model->getRelatedProducts($product['id']);
//
//        //характеристики товара
//        $product_properties = $model->getProductPropertiesByIds($product['id']);
//        //debug($product_properties);
//        $propertiesArr = [];
//        foreach($product_properties as $property){
//            $propertiesArr[$property['p_name']][$property['id']]['product_id']     =  $property['product_id'];
//            $propertiesArr[$property['p_name']][$property['id']]['pv_name']        =  $property['pv_name'];
//            $propertiesArr[$property['p_name']][$property['id']]['pv_value']       =  $property['pv_value'];
//            $propertiesArr[$property['p_name']][$property['id']]['price']          =  $property['price'];
//            $propertiesArr[$property['p_name']][$property['id']]['old_price']      =  $property['old_price'];
//            $propertiesArr[$property['p_name']][$property['id']]['count']          =  $property['count'];
//            $propertiesArr[$property['p_name']][$property['id']]['weight']         =  $property['weight'];
//        }
//        $product['propertiesArr'] = $propertiesArr;
//        //debug($product);
//
//        //Зависимые характеристики товара
//        $mods = $model->getProductDependenciesById(null, $product['id']);
//        //debug($mods);
//
//        foreach($mods as $k=>$v){
//            $parent_name = $v['p_name'];
//            $child_name = $v['ch_name'];
//        }
//
//        $parentArr = [];
//        foreach($mods as $myRow){
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['product_id']       =  $myRow['product_id'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['parent_name']      =  $myRow['p_name'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['child_name']       =  $myRow['ch_name'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['child_val']        =  $myRow['ch_val'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['child_value']      =  $myRow['ch_value'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['count']            =  $myRow['count'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['weight']           =  $myRow['weight'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['price']            =  $myRow['price'];
//            $parentArr   [$myRow['pv_name']]   [$myRow['id']]  ['old_price']        =  $myRow['old_price'];
//        }
//        //debug($parentArr);
//
//        $childsArr = [];
//        foreach ($parentArr as $size => $colors){
//            foreach($colors as $key => $value){
//                $childsArr[$value['child_val']][$value['child_value']][$key] = $size;
//            }
//        }
//        //debug($childsArr);
//        $product['parentArr'] = $parentArr;
//        $product['childsArr'] = $childsArr;
//        debug($product);
//        //галерея из фотографий
//        $gallery = $model->getProductImages($product['id']);
//        $product['gallery'] = $gallery;
        View::setMeta('Редактирование товара', '', '');
        $this->set(compact('product', 'gallery', 'vendors'));

    }



}