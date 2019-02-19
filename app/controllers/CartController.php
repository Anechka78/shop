<?php


namespace app\controllers;

use app\models\Cart;
use app\models\Product;
use app\models\User;
use im\core\App;
use im\core\base\View;
use im\core\Validator;


class CartController extends AppController{
    /**
     * Добавление товара в Корзину
     */

    public function addAction(){
        $id = !empty($_POST['id']) ? trim((int)$_POST['id']) : null;
        $ID = $id;
        //зависимых характеристик родитель-ребенок может быть всего 1 и это - id характеристики из таблицы properties_dependences
        if (!empty($_POST['pd_id'])){
            $pd_id = trim($_POST['pd_id'][0]);
            $ID .= '-pd'.$pd_id;
        }
        //получаем массив прочих характеристик товара из таблицы product_properties_values
        if (!empty($_POST['pp_id'])){
            $pp_id = $_POST['pp_id'];
            foreach ($pp_id as $k=>$v){
                $ID .= '-pp'.$v;
            }
        }

        $model = new Product();
        // Формируем Товар для корзины в сессии или БД
        if(!isset($pp_id) && !isset($pd_id)){
            $product = $model->getOneProduct($id);
        }elseif(!isset($pp_id)){
            $product = $model->getOneProduct($id, $pd_id);
        }elseif(!isset($pd_id)){
            $product = $model->getOneProduct($id, $pp_id);
        }else{
            $product = $model->getOneProduct($id, $pp_id, $pd_id);
        }
       $user = User::isUser();

        //Если найден пользователь, который авторизован - кладем товар в таблицу UserCart БД и корзину достаем из БД
        if($user){
            $user_id = $user['id'];
            $resData = array();
            if(!isset($_SESSION['cart.currency'])){
                $_SESSION['cart.currency'] = App::$app->getProperty('currency');
            }
            $model = new Cart();
            $user_cart = $model->addToUserCart($user_id, $ID, $product, $qty = 1);
            if(!$user_cart){
                $resData['error'] = 1;
                $resData['message'] = 'При добавлении товара произошла ошибка. Возможно, такого кол-ва нет на складе.';
            }else{
               $cart = $model->getUsersCart($user_id);
            }

            if(!$cart){
                $resData['error'] = 1;
                $resData['message'] = 'Чего-то не то произошло';
            }else{
                $_SESSION['user']['cart']['totalsum'] = $cart['totalsum'];
                $_SESSION['user']['cart']['totalqty'] = $cart['totalqty'];
                $resData['success'] = 1; // записываем в результирующие данные успех операции
                $resData['message'] = 'Товар успешно добавлен в Корзину';
                $resData['cart'] = $cart;
            }
            die(json_encode($resData));

        }else{
            $cart = new Cart();
            //debug($cart); die();
            $cartInfo = $cart->addToCart($product, $qty = 1);
            //debug($cartInfo); die();

            $resData = array();
            if(!$cartInfo){
                $resData['error'] = 1;
                $resData['message'] = 'Такого количества товара в наличии нет';
            }else{
                $resData['success'] = 1; // записываем в результирующие данные успех операции
                $resData['message'] = 'Товар успешно добавлен в Корзину';
                $resData['cart'] = $cartInfo;
            }
            die(json_encode($resData));
        }
    }
/*
 * Загрузка страницы Корзины
 */
    public function indexAction(){
        $this->layout = 'cart';
        View::setMeta('Корзина покупок', '', '');
        $model = new Cart();

        $user = User::isUser();
        if($user){
            $user_id = $user['id'];
            $cart = $model->findEqual($user_id, 'user_id', 'users_cart');
            //debug($cart); die();
            $products=[];

            //заполняем массив продуктов
            foreach($cart as $key=>$item){
                $id = $item['product_id'];
                $item['json_product'] = json_decode($item['json_product'], true);
                $products[$id]['qty'] = $item['count'];
                $products[$id]['product'] = $item['json_product'];
                $products[$id]['price'] = $model->getValueFromArrays($products[$id]['product'], 'price');
                $products[$id]['weight'] = Cart::getValueFromArray($products[$id]['product'], 'weight');
                $products[$id]['count'] = $model->getValueFromArrays($products[$id]['product'], 'count');
                $products[$id]['summ'] = $products[$id]['price'] * $products[$id]['qty'];
            }
debug($products); die();
            //Проверяем, есть ли товар в наличии, если нет - удаляем из корзины
            //Если же кол-во товара меньше, чем заказанное - делаем кол-во товара в корзине равным тому, что в наличии
            foreach($products as $id=>$pr) {
                if($pr['count'] == 0) {
                    $model->removeFromUsersCart($user_id, $id);
                    unset($products[$id]);
                }else if($pr['qty'] > $pr['count']){
                    $products[$id]['qty'] = $products[$id]['count'];
                }
            }
            $totalWeight =  array_sum(array_column($products, 'weight'));
            $totalSum = array_sum(array_column($products, 'summ'));
            $totalQty = array_sum(array_column($products, 'qty'));
            $cart = [];
            $cart['products'] = $products;
            $cart['totalsum'] = $totalSum;
            $cart['totalqty'] = $totalQty;
            $cart['totalweight'] = $totalWeight;

            $_SESSION['user']['cart']['totalsum'] = $totalSum;
            $_SESSION['user']['cart']['totalqty'] = $totalQty;

            $currency = isset($_SESSION['cart.currency']) ? $_SESSION['cart.currency'] : $_SESSION['cart.currency'] = App::$app->getProperty('currency');

            $itemsArr = $cart['products'];

            $qty = $cart['totalqty'];
            $sum = $cart['totalsum'];
            $weight = $cart['totalweight'];
//debug($cart); die();
            //Передаем данные в вид
            $this->set(compact('itemsArr', 'currency', 'qty', 'sum', 'weight'));

        }else{
            $cart = $model->getCart();
            //debug($cart); die();
            $itemsArr = $cart['itemsArr'];
            $currency = $cart['currency'];

            $qty = $cart['qty'];
            $sum = $cart['sum'];
            $weight = $cart['weight'];

            //Передаем данные в вид
            $this->set(compact('itemsArr', 'currency', 'qty', 'sum', 'weight'));
        }


    }
/*
 * Изменение кол-ва товаров в корзине
 */
    public function changeAction(){
        $id = !empty($_POST['id']) ? trim($_POST['id']) : null;
        $qty = !empty($_POST['qty']) ? trim((int)$_POST['qty']) : 0;

        $model = new Cart();

        $user = User::isUser();
        //Если пользователь зарегистрирован и меняет свою корзину
        if($user) {
            $user_id = $user['id'];
            $resData = $model->changeUsersCart($id, $qty, $user_id);
            //debug($resData); die();
            die(json_encode($resData));
        }else{
            $resData = $model->changeCart($id, $qty);
            die(json_encode($resData));
        }
    }

    /**
     * Удаление товара из Корзины
     */

    public function removefromcartAction(){
        $itemToDel = trim($_POST['id']);
        $model = new Cart();
        $user = User::isUser();

        $resData = array(); //ининциализируем переменную, куда записываем результирующие данные, которые будет выдавать наша функция
        if($user) {
            $user_id = $user['id'];
            $remove = $model->removeFromUsersCart($user_id, $itemToDel);
            if($remove){
                $resData['success'] = 1; // записываем в результирующие данные успех операции
                $resData['cart'] = $remove;
            }else{
                $resData['success'] = 0;
            }
        }else{
            $remove = $model->removeFromCart($itemToDel);
            if($remove){
                $resData['success'] = 1; // записываем в результирующие данные успех операции
                $resData['cart'] = $_SESSION['cart'];
            }else{
                $resData['success'] = 0;
            }
        }

        die(json_encode($resData));
    }
/*
 * Функция очистки Корзины
 */
    public function clearAction(){
        $model = new Cart();
        $delCart = $model->deleteCart();

        $resData = array();
        if($delCart){
            $resData['success'] = 1;
        } else{
            $resData['success'] = 0;
        }
        die(json_encode($resData));
    }

    public function orderAction(){
        $resData = array();

        foreach([   'name'  => ['value'=>$_POST['userInfo']['name'], 'message'=>'Поле ФИО заполнено неверно'],
                    'email' => ['value'=>$_POST['userInfo']['email'], 'message'=>'Поле email заполнено неверно'],
                    'adress' => ['value'=>$_POST['userInfo']['adress'], 'message'=>'Поле адрес заполнено неверно'],
                    'phone' => ['value'=>$_POST['userInfo']['phone'], 'message'=>'Поле телефон заполнено неверно'],
                    'note' => ['value'=>$_POST['userInfo']['note'], 'message'=>'Поле примечание заполнено неверно'],
                ] as $key=>$val){
            //echo $val['value'].PHP_EOL;
            $res = Validator::$key($val['value'], $val['message']);
            if($res !== true){
                $resData['success'] = 0;
                $resData['message'] = $res;
                die(json_encode($resData));
            }
        }

        $order = $_POST['ItemsInOrder'];
        //debug($order); die();
        $products = [];
        foreach($order as $num => $item){
            $multiple_id = $item['multiple_id'];
            $id = explode('-', $multiple_id);
            $products[$num]['qty'] = $item['qty'];
            $products[$num]['product_id'] = $id[0];
            $q = count($id);
            for($i=1; $i<$q; $i++){
                if(substr($id[$i], 0, 2) == 'pp'){
                    $products[$num]['pp'][] = substr($id[$i], 2);
                }elseif(substr($id[$i], 0, 2) == 'pd'){
                    $products[$num]['pd'] = substr($id[$i], 2);
                }
            }
        }
        //debug($products); die();
        $model = new Product();
        $product = [];
        foreach($products as $k=>$v){
            if((!isset($v['pp'])) && (!isset($v['pd']))){
                $product[] = $model->getOneProduct($v['product_id']);
            }elseif(!isset($v['pd'])){
                $product[] = $model->getOneProduct($v['product_id'], $v['pp'], '');
            }elseif(!isset($v['pp'])){
                $product[] = $model->getOneProduct($v['product_id'], [], $v['pd']);
            }else{
                $product[] = $model->getOneProduct($v['product_id'], $v['pp'], $v['pd']);
            }
        }
//        $cart = new Cart();
//        foreach($product as $k=>$row){
//            $product_price = $cart->getValueFromArrays($row, 'price');
//            $product_count = $cart->getValueFromArrays($row, 'count');
//            $product_weight = $cart->getValueFromArrays($row, 'weight');
//            $product[$k]['itemPrice'] = $product_price;
//            $product[$k]['itemCount'] = $product_count;
//            $product[$k]['itemWeight'] = $product_weight;
//        }

        debug($product); die();

        $user = User::isUser();
        if($user){
            $user_id = $user['id'];
        }



    }

}