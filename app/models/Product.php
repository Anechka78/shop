<?php

namespace app\models;


use im\core\base\Model;
use im\core\Db;

class Product extends Model
{
    private static $products_table = 'products';
    private static $related_table = 'related_products';
    private static $gallery_table = 'productimages';
    private static $properties_table = 'properties';
    private static $properties_values_table = 'properties_values';
    private static $properties_dependences_table = 'properties_dependences';
    private static $product_prop_val_table = 'product_properties_values';
    private static $product_json_table = 'jsonproduct';

    /**
     * Получаем данные о продукте по его алиасу
     * @param $alias алиас продукта
     * @return mixed возвращает все поля из таблицы products БД
     */
    public function getProductByAlias($alias)
    {
        $sql = "SELECT pr.*, cat.name as cat_name, cat.alias as cat_alias, vn.name as vn_name FROM " . self::$products_table . " AS pr
                LEFT JOIN `categories` AS `cat` ON pr.category_id = cat.id
                LEFT JOIN `vendor` AS `vn` ON pr.vendor = vn.id
                WHERE pr.alias = ?";
        $product = $this->findBySql($sql, [$alias])[0];

        return $product;
    }

    /**
     * Получаем данные о продукте из таблицы products
     * @param $id - идентификатор продукта из таблицы products
     * @return mixed
     */
    public function getProductById($id){
        $sql = "SELECT `id`, `name`, `alias`, `price`, `image`, `count`, `weight`, `status` FROM ". self::$products_table."
                WHERE id = ?";
        $product = $this->findBySql($sql, [$id])[0];

        return $product;
    }

     /**
     * Вносим данные о продукте в таблицу products_search для поиска по ним
     * @param $id - идентификатор продукта из таблицы products
     * @param $name - название продукта
     * @param $description - описание продукта
     */
    public function setProductToSearch($id, $name, $description){
        $sql = "INSERT  INTO `products_search` (`product_id`, `product_name`,`product_description`) VALUES (
                 ?, ?, ?)";
        $result = $this->findBySql($sql, [$id, $name, $description]);
    }
    /*
    //Вносим данные о продукте в таблицу jsonproduct
    public function jsonproduct($alias, $product){
        $sql = "INSERT  INTO `jsonproduct` (`product_alias`,`product_json`) VALUES (
                 ?, ?)";
        $jsonprod = $this->findBySql($sql, [$alias, $product]);
    }
    //Получаем данные о продукте из таблицы jsonproduct
    public function getProductByJson($alias){
        $sql = "SELECT * FROM ". self::$product_json_table."
                WHERE product_alias = ?";
        $product = $this->findBySql($sql, [$alias])[0]['product_json'];

        return $product;
    }*/

    /**
     * Получаем простые характеристики товара для корзины из таблицы product_properties_values
     * @param $id идентификатор продукта из таблицы products или массив идентификаторов характеристик
     *        - id характеристик из таблицы product_properties_values
     * @return mixed
     */
    public function getProductPropertiesByIds($id){
        if(is_array($id)){
            $in = join(',', array_fill(0, count($id), '?'));
            $sql = "SELECT pr.*, pv.name as pv_name, pv.value as pv_value, p.name as p_name FROM ". self::$product_prop_val_table." AS pr
                LEFT JOIN `properties_values` AS `pv` ON pr.prop_val_id = pv.id
                LEFT JOIN `properties` AS `p` ON pv.property_id = p.id
                WHERE pr.id IN ($in)";
            $product_properties = $this->findBySql($sql, $id);
        }else{
            $sql = "SELECT pr.*, pv.name as pv_name, pv.value as pv_value, p.name as p_name FROM ". self::$product_prop_val_table." AS pr
                LEFT JOIN `properties_values` AS `pv` ON pr.prop_val_id = pv.id
                LEFT JOIN `properties` AS `p` ON pv.property_id = p.id
                WHERE pr.product_id = ?";
            $product_properties = $this->findBySql($sql, [$id]);
        }
        return $product_properties;
    }

    /**
     * Получаем данные о сложно-связанных характеристиках товара (типа размер-цвет) из таблицы properties_dependences
     * @param string $pd_id идентификатор характеристики из таблицы properties_dependences
     * @param $id идентификатор продукта из таблицы products
     * @return mixed массив характеристик
     */
    public function getProductDependenciesById($pd_id = '', $id){
        if($pd_id){
            $sql = "SELECT pd.id, pd.product_id, pd.count, pd.weight, pd.price, p.name as p_name, pv.name as pv_name, p_2.name as ch_name, pv_2.name as ch_val, pv_2.value as ch_value
                FROM ". self::$properties_dependences_table." AS pd
                LEFT JOIN ". self::$properties_table." AS `p` ON pd.parent_property_name = p.id
                LEFT JOIN ". self::$properties_table." AS `p_2` ON pd.child_property_name = p_2.id
                LEFT JOIN ". self::$properties_values_table." AS `pv` ON pd.parent_property_names = pv.id
                LEFT JOIN ". self::$properties_values_table." AS `pv_2` ON pd.child_property_names = pv_2.id
                WHERE pd.id = ? AND pd.product_id = ?";
            $mods = $this->findBySql($sql, [$pd_id, $id]);
        }else{
            $sql = "SELECT pd.*, p.name as p_name, pv.name as pv_name, p_2.name as ch_name, pv_2.name as ch_val, pv_2.value as ch_value
                FROM ". self::$properties_dependences_table." AS pd
                LEFT JOIN ". self::$properties_table." AS `p` ON pd.parent_property_name = p.id
                LEFT JOIN ". self::$properties_table." AS `p_2` ON pd.child_property_name = p_2.id
                LEFT JOIN ". self::$properties_values_table." AS `pv` ON pd.parent_property_names = pv.id
                LEFT JOIN ". self::$properties_values_table." AS `pv_2` ON pd.child_property_names = pv_2.id
                WHERE pd.product_id = ?";
            $mods = $this->findBySql($sql, [$id]);
        }

        return $mods;
    }

    /**
     * Получаем выбору рекомендуемых продуктов
     * @param $id идентификатор продукта из таблицы products
     * @return mixed
     */
    public function getRelatedProducts($id){
        $relatedSql = "SELECT `name`, `alias`, `price`, `old_price`, `image` FROM ". self::$related_table." JOIN ". self::$products_table. "
                      ON products.id = related_products.related_id
                      WHERE related_products.product_id = ?";
        $related = $this->findBySql($relatedSql, [$id]);

        return $related;
    }

    /**
     * Получаем галерею изображений для продукта
     * @param $id идентификатор продукта из таблицы products
     * @return mixed
     */
    public function getProductImages($id){
        $sql = "SELECT * FROM ". self::$gallery_table." WHERE product_id = ?";
        $gallery = $this->findBySql($sql, [$id]);

        return $gallery;
    }

    /**
     * Получаем просмотренные продукты
     * @param $r_viewed идентификаторы продуктов из таблицы products
     * @return mixed
     */
    public function getRecentlyViewedProducts($r_viewed){
        $in  = str_repeat('?,', count($r_viewed) - 1) . '?';
        //debug($in);
        $sql = "SELECT `name`, `alias`, `price`, `old_price`, `image` FROM ". self::$products_table." WHERE `id` IN ($in) LIMIT 4";
        $recentlyViewed = $this->findBySql($sql, $r_viewed);
        return $recentlyViewed;
    }

    /**
     * Заносим в куки идентификаторы просмотренных клиентом ранее продуктов
     * @param $id идентификатор продукта из таблицы products
     */
    public function setRecentlyViewed($id){
        $recentlyViewed = $this->getAllRecentlyViewed();
        if(!$recentlyViewed){
            setcookie('recentlyViewed', $id, time() + 3600*24, '/');
        }else{
            $recentlyViewed = explode('.', $recentlyViewed);
            if(!in_array($id, $recentlyViewed)){
                $recentlyViewed[] = $id;            }
                $recentlyViewed = implode('.', $recentlyViewed);
                setcookie('recentlyViewed', $recentlyViewed, time() + 3600*24, '/');
        }
    }

    /**
     * Получение просмотренных ранее id товаров
     * @return array|bool Получаем массив идентификаторов продуктов, которые были просмотрены клиентом ранее
     */
    public function getRecentlyViewed(){
        if(!empty(($_COOKIE['recentlyViewed']))){
            $recentlyViewed = $_COOKIE['recentlyViewed'];
            $recentlyViewed = explode('.', $recentlyViewed);
            return array_slice($recentlyViewed, -4);
        }
        return false;
    }

    /**
     * Получение из кук всех просмотренных ранее товаров
     * @return bool
     */
    public function getAllRecentlyViewed(){
        if(!empty ($_COOKIE['recentlyViewed'])){
            return $_COOKIE['recentlyViewed'];
        }
        return false;
    }


/******************CART*************************/

    /**
     * Достать один продукт по его $id, а также - по характеристикам, которые у него есть
     * @param $id
     * @param array $pp_id
     * @param string $pd_id
     * @return array|bool
     */
    public function getOneProduct($id, $pp_id = [], $pd_id = ''){
        $result = $this->getProductById($id);

        //Оставляем в массиве продукта только те данные, которые нам необходимы для Корзины
        $product=[];
        $product['id'] = $result['id'];
        $product['name'] = $result['name'];
        $product['alias'] = $result['alias'];
        $product['price'] = $result['price'];
        $product['image'] = $result['image'];
        $product['count'] = $result['count'];
        $product['weight'] = $result['weight'];
        $product['status'] = $result['status'];

        if(!$product){
            return false;
        }
        if(count($pp_id) > 0){
            $product_properties = $this->getProductPropertiesByIds($pp_id);
            foreach($product_properties as $k=>$v){
                $product['prValArr'][$k]['id'] = $v['id'];
                $product['prValArr'][$k]['p_name'] = $v['p_name'];
                $product['prValArr'][$k]['pv_name'] = $v['pv_name'];
                $product['prValArr'][$k]['price'] = $v['price'];
                $product['prValArr'][$k]['count'] = $v['count'];
                $product['prValArr'][$k]['weight'] = $v['weight'];
            }
        }

        if(!empty($pd_id)){
            $product_dependencies = $this->getProductDependenciesById($pd_id, $id)[0];
            $product['prDepArr'] = $product_dependencies;
        }
        //$cart = new Cart();

            $product['itemPrice'] = $this->getValueFromArrays($product, 'price');
            $product['itemCount'] = $this->getValueFromArrays($product, 'count');
            $product['itemWeight'] = $this->getValueFromArrays($product, 'weight');
        //debug($product); die();

        return $product;
    }
/******************CART*************************/

}