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

}