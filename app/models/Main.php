<?php

namespace app\models;

use im\core\base\Model;

class Main extends Model
{
    public $table = 'products';

    public function findLast($limit, $table=''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        $sql = "SELECT * FROM $table ORDER BY `id` DESC LIMIT ?";
        return $this->pdo->query($sql, [$limit]);
    }
    //public $pk = 'categoty_id';//если хотим переопределить столбец выборки для метода findOne() Model.php

    public function findHits($limit, $table=''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        $sql = "SELECT * FROM $table WHERE `hit` = '1' AND `status` = '1' LIMIT ?";
        return $this->pdo->query($sql, [$limit]);
    }


}