<?php

namespace app\models;

use im\core\base\Model;

class Search extends Model{

    private static $products_search_table = 'products_search';
    private static $products_table = 'products';

    public function getResults($query){
        $sql = "SELECT `name`, `alias`, `price`, `old_price`, `image`  FROM ". self::$products_table."
                WHERE `id` IN (SELECT `product_id` FROM ". self::$products_search_table." WHERE MATCH (product_name, product_description) AGAINST (?)) AND `status` = '1'";
        $products = $this->findBySql($sql, [$query]);

        return $products;

    }

}