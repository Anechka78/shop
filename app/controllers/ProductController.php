<?php

namespace app\controllers;

use app\models\Breadcrumbs;
use app\models\Category;
use app\models\Product;
use im\core\base\View;
use im\libs\Timer;

class ProductController extends AppController
{
    public function viewAction(){
        $this->layout = 'product';
        $alias = $this->route['alias'];
        Timer::start();

        //Получаем товар без характеристик из таблицы products
        $model = new Product();
        $product = $model->getProductByAlias($alias);
        //debug($product);

        //Получаем мета-данные
        $title = $product['title'];
        $description = $product['meta_desc'];
        //$keywords = $product['keywords']; раскомментировать, если понадобится, в целом - устарело
        View::setMeta($title, $description, 'Ключевые слова для товара');

        //хлебные крошки
        $breadcrumbs = Breadcrumbs::getBreadcrumbs($product['category_id'], 'alias', $product['name']);

        //связанные товары
        $related = $model->getRelatedProducts($product['id']);
        $product['related'] = $related;

        //просмотренные ранее товары из кук
        $r_viewed = $model->getRecentlyViewed();
        $recentlyViewed = null;
        if($r_viewed){
            $recentlyViewed = $model->getRecentlyViewedProducts($r_viewed);
        }

        //запись в куки текущего товара
        $model->setRecentlyViewed($product['id']);

        //галерея из фотографий
        $gallery = $model->getProductImages($product['id']);
        $product['gallery'] = $gallery;

        //характеристики товара
        $product_properties = $model->getProductPropertiesByIds($product['id']);
        //debug($product_properties);
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
        $product['propertiesArr'] = $propertiesArr;
        //debug($product);

        //Зависимые характеристики товара
        $mods = $model->getProductDependenciesById(null, $product['id']);
        //debug($mods);

        foreach($mods as $k=>$v){
            $parent_name = $v['p_name'];
            $child_name = $v['ch_name'];
        }

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
        //debug($parentArr);

        $childsArr = [];
        foreach ($parentArr as $size => $colors){
            foreach($colors as $key => $value){
                $childsArr[$value['child_val']][$value['child_value']][$key] = $size;
            }
        }
        //debug($childsArr);
        $product['parentArr'] = $parentArr;
        $product['childsArr'] = $childsArr;



        //debug($product);

        echo Timer::finish() . ' сек.';

        /*
         //Заносим id товара, название и описание в таблицу products_search для будущего поиска - нужно на этапе добавления нового товара!!!!
        //$search = $model->setProductToSearch($product['id'], $product['name'], $product['description']);


        // функция как загнать массив с данными о продукте в json виде в таблицу jsonproduct mysql
        //и как пото вытащить из нее тот же массив продукта со всеми данными. Ускорение в 10-12 раз.

        //Нужно раскомментировать, добавить хлебные крошки, 'parent_name', 'child_name'
        //Суть в том, что мы кодируем весь товар с его характеристиками и фотографиями и пр. в jsone БД.

        //$data = json_encode($product);
        //$gotojson = $model->jsonproduct($alias, $data);


        Timer::start();
        $model = new Product();
        $product = $model->getProductByJson($alias);
        $product = json_decode($product, true);
        echo Timer::finish() . ' сек.';
        */


        //Передаем данные в вид
        $this->set(compact('product', 'gallery', 'recentlyViewed', 'breadcrumbs', 'parent_name', 'child_name'));

    }

}