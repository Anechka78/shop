<?php

namespace im\widgets\currency;


use im\core\base\Model;

class CurrencyModel extends Model
{
    public $table = 'currency';

    public function findCurrencies($sort, $table = ''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу

        $sql = "DESCRIBE $table";
        $table_info = $this->pdo->query($sql, []);

        $columnFlag = false;
        foreach($table_info as $key=>$info){
            if($info['Field'] == $sort)
                $columnFlag = true;
        }

        if ($columnFlag){
            $sql = "SELECT * FROM $table ORDER BY ".$sort." DESC";
            return $this->pdo->query($sql, []);
        }else{
            echo "Сортировка по столбцу {$sort} невозможна";
        }
    }

}