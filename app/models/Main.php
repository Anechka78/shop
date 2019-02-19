<?php

namespace app\models;

use im\core\base\Model;

class Main extends Model
{
    public $table = 'products';

    public function findLast($limit, $table=''){
        $table = $table ?: $this->table; //���� �������� ������� - ����� ��, ��� - ����� ��������� � ������ �������
        $sql = "SELECT * FROM $table ORDER BY `id` DESC LIMIT ?";
        return $this->pdo->query($sql, [$limit]);
    }
    //public $pk = 'categoty_id';//���� ����� �������������� ������� ������� ��� ������ findOne() Model.php

    public function findHits($limit, $table=''){
        $table = $table ?: $this->table; //���� �������� ������� - ����� ��, ��� - ����� ��������� � ������ �������
        $sql = "SELECT * FROM $table WHERE `hit` = '1' AND `status` = '1' LIMIT ?";
        return $this->pdo->query($sql, [$limit]);
    }


}