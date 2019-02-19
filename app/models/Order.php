<?php

namespace app\models;


use im\core\base\Model;

class Order extends Model{
    public function getAllOrders($start, $perpage){
        $sql = "SELECT ord.*, u.name as user_name FROM `orders` AS `ord`
                    JOIN `userss` AS `u` ON ord.user_id = u.id ORDER BY ord.status LIMIT :start, :perpage";
        $orders = $this->findBySql($sql, array('start' => $start, 'perpage' => $perpage));
        return $orders;
    }

}