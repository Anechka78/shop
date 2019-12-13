<?php

namespace app\models;

use im\core\App;
use im\core\base\Model;

class Category extends Model
{
    public $table = 'categories';
    public $arr = [];

    public function findProducts ($table = 'products', $arr){
        //���� ����� ���������� �� ����� ������ cats, � ����� ��
       // $preparedInValues = implode(',', array_fill(0, count($arr), '?'));

        $table = $table ?: $this->table; //���� �������� ������� - ����� ��, ��� - ����� ��������� � ������ �������
        $sql = "SELECT `name`, `alias`, `price`, `old_price`, `image` FROM ". $table. " WHERE `category_id` IN ({$arr})";
        return $this->pdo->query($sql, []);
    }

    public function getIds($id){
        $cats = App::$app->getProperty('cats');
        $ids = null;
        foreach($cats as $k => $v){
            if($v['parent_id'] == $id){
                $ids .= $k . ',';
                $ids .= $this->getIds($k);
            }
        }
        return $ids;
    }


}