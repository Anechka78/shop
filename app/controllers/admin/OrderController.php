<?php

namespace app\controllers\admin;


use app\models\Order;
use im\core\base\View;
use im\libs\Pagination;

class OrderController extends AppController{

    public function indexAction(){
        $model = new Order();
        $page = isset($_GET['page'])? (int)$_GET['page'] : 1;
        $perpage = 3;
        $count = $model->selectCount('orders');
        $pagination = new Pagination($page, $perpage, $count);
        $start = $pagination->getStart();

        $orders = $model->getAllOrders($start, $perpage);
        //debug($orders);
        View::setMeta('Список заказов', '', '');
        $this->set(compact('orders', 'pagination', 'count'));
    }

}