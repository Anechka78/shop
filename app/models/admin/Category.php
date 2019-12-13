<?php

namespace app\models\admin;


use im\core\base\Model;

class Category extends Model{

    public function selectCountChildren($table, $field, $id){
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM $table WHERE $field = $id");
        $nRows = $stmt[0]['COUNT(*)'];
        return $nRows;
    }
    public function deleteCategory($table, $id){

    }

    public function createFilterCategory($category_id){
        $sql = "CREATE TABLE `filter_"."$category_id"."` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `product_id` INT(11) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
        //var_dump($sql); die();
        return $this->pdo->query($sql, []);
    }

}