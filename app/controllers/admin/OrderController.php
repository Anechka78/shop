<?php

namespace app\controllers\admin;


use app\models\Order;
use im\core\base\View;
use im\libs\Pagination;

class OrderController extends AppController{

    public function indexAction(){
        $model = new Order();
        $page = isset($_GET['page'])? (int)$_GET['page'] : 1;
        $perpage = 5;
        $count = $model->selectCount('orders');
        $pagination = new Pagination($page, $perpage, $count);
        $start = $pagination->getStart();
        $orders = $model->getAllOrders($start, $perpage);

        foreach($orders as $id=>$val){
            $orders[$id]['shipping_info'] = json_decode($val['shipping_info'], true);
        }

        View::setMeta('Список заказов', '', '');
        $this->set(compact('orders', 'pagination', 'count'));
    }

    public function editAction(){
        $id = $this->getRequestID();
        $model = new Order();
        $order_info = $model->findOne($id, 'id', 'orders')[0];
        //debug($order_info);
        $order_info['shipping_info'] = json_decode($order_info['shipping_info'], true);
        $order_info['currency'] = json_decode($order_info['currency'], true);
        $order_items = $model->findEqual($id, 'order_id', 'order_items');
        foreach($order_items as $id=>$item){
            $order_items[$id]['product_info'] = json_decode($item['product_info'], true);
        }
        //debug($order_items);
        if($order_info['user_id'] == 0){
            $user_info['name'] = 'Незарегистрированный пользователь';
            $user_info['email'] = '-';
            $user_info['phone'] = '-';
            $user_info['address'] = '-';
        }else{
            $user_info = $model->findOne($order_info['user_id'], 'id', 'userss')[0];
            unset($user_info['pwd1']);
            unset($user_info['role']);
            unset($user_info['login']);
        }

        View::setMeta('Редактирование заказа', '', '');
        $this->set(compact('order_info', 'order_items', 'user_info'));

    }

}