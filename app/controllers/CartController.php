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
        }elseif(!isset($pp_id) || $pp_id==[]){
            $product = $model->getOneProduct($id, [], $pd_id);
        }elseif(!isset($pd_id)){
            $product = $model->getOneProduct($id, $pp_id);
        }else{
            $product = $model->getOneProduct($id, $pp_id, $pd_id);
        }
        //debug($ID); die();
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
                die(json_encode($resData));
            }else{
               $addToSession = $model->addToUsersSessionCart($product['itemPrice'], $product['itemWeight']);
            }

            if(!$addToSession){
                $resData['error'] = 1;
                $resData['message'] = 'Чего-то не то произошло';
                die(json_encode($resData));
            }else{
                $resData['success'] = 1; // записываем в результирующие данные успех операции
                $resData['message'] = 'Товар успешно добавлен в Корзину';
                $resData['cart'] = $addToSession;
            }
            die(json_encode($resData));

        }else{
            $cart = new Cart();
            $cartInfo = $cart->addToCart($product, $qty = 1, $ID);
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
//debug($_SESSION);
        $user = User::isUser();
        if($user){
            $user_id = $user['id'];
            $cart = $model->getUsersCart($user_id);

            $currency = isset($_SESSION['cart.currency']) ? $_SESSION['cart.currency'] : $_SESSION['cart.currency'] = App::$app->getProperty('currency');

            $itemsArr = $cart['products'];
            $qty = $cart['totalqty'];
            $sum = $cart['totalsum'];
            $weight = $cart['totalweight'];
            //debug($sum); die();

            //Передаем данные в вид
            $this->set(compact('itemsArr', 'currency', 'qty', 'sum', 'weight'));

        }else{
            $cart = $model->getCart();
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
//debug($_SESSION); die();
        $model = new Cart();

        $user = User::isUser();
        //Если пользователь зарегистрирован и меняет свою корзину
        //$resData = $model->changeUsersCart($id, $qty, $user['id']) ?? $model->changeCart($id, $qty);
        if($user) {
            $user_id = $user['id'];
            //$resData = $model->changeUsersCart($id, $qty, $user_id);
            $resData = $model->changeUsersCart($id, $qty, $user['id']);
            //debug($resData); die();
            die(json_encode($resData));
        }else{
            $resData = $model->changeCart($id, $qty);
            die(json_encode($resData));
        }
       // die(json_encode($resData));
    }

    /**
     * Удаление товара из Корзины
     */

    public function removefromcartAction(){
        $itemToDel = trim($_POST['id']);
        $qty = trim($_POST['qty']);
        $summ = trim($_POST['summ']);
        $weight = trim($_POST['weight']);
        $model = new Cart();
        $user = User::isUser();

        $resData = array(); //ининциализируем переменную, куда записываем результирующие данные, которые будет выдавать наша функция
        if($user) {
            $user_id = $user['id'];
            $remove = $model->removeFromUsersCart($user_id, $itemToDel);
            if($remove){
                $_SESSION['user']['cart']['totalsum'] = $_SESSION['user']['cart']['totalsum'] - $summ;
                $_SESSION['user']['cart']['totalqty'] = $_SESSION['user']['cart']['totalqty'] - $qty;
                $_SESSION['user']['cart']['totalweight'] = $_SESSION['user']['cart']['totalweight'] - $weight;
                if($_SESSION['user']['cart']['totalsum'] == 0){
                    unset($_SESSION['user']['cart']);
                }
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
        if(isset($_SESSION['user'])){
            $delCart = $model->deleteUserCart($_SESSION['user']['id']);
        }else{
            $delCart = $model->deleteCart();
        }

        $resData = array();
        if($delCart){
            $resData['success'] = 1;
        } else{
            $resData['success'] = 0;
        }
        die(json_encode($resData));
    }

/*
 * Удаление разницы товаров из Корзины при авторизации пользователя
 */
    public function clearDiffAction(){
        $model = new Cart();
        $resData = array();
        $totalweight = $_SESSION['cart']['totalweight'];
        $totalsum = $_SESSION['cart']['totalsum'];
        $totalqty = $_SESSION['cart']['totalqty'];
        $cartCurr = $_SESSION['cart.currency'];
        $delCart = $model->deleteUserCart($_SESSION['user']['id']);
        if($delCart){
            $_SESSION['cart.currency'] = $cartCurr;
            $addToUsersCart = $model->addToUsersCartDiff($_SESSION['user']['id'], $_SESSION['cart']['products']);
            if($addToUsersCart){
                $_SESSION['user']['cart'] = [];
                $_SESSION['user']['cart']['totalweight'] = $totalweight;
                $_SESSION['user']['cart']['totalsum'] = $totalsum;
                $_SESSION['user']['cart']['totalqty'] = $totalqty;
                unset($_SESSION['user']['cart_diff']);
                $resData['success'] = 1;
                $resData['message'] = 'Изменения в Корзине совершены успешно';
            }else{
                $resData['success'] = 0;
                $resData['message'] = 'Что-то пошло в Корзине при добавлении не так:((';
            }
        }else{
            $resData['success'] = 0;
            $resData['message'] = 'Что-то пошло в Корзине при удалении не так:((';
        }
        die(json_encode($resData));
    }
/*
 * Добавление разницы товаров из Корзины при авторизации пользователя
 */
    public function addDiffAction(){
        $model = new Cart();
        $resData = array();
        $products = $_SESSION['cart']['products'] + $_SESSION['user']['cart_diff'];
        unset($_SESSION['cart']['products']);
        $cartCurr = $_SESSION['cart.currency'];
        $delCart = $model->deleteUserCart($_SESSION['user']['id']);
        if($delCart){
            $_SESSION['cart.currency'] = $cartCurr;
            $addToUsersCart = $model->addToUsersCartDiff($_SESSION['user']['id'], $products);
            if($addToUsersCart){
                foreach($products as $id=>$item){
                    $resData['cart']['totalqty'] = array_sum(array_column($products, 'qty'));
                    isset($resData['cart']['totalsum']) ? $resData['cart']['totalsum'] += $item['qty']*$item['itemPrice']: $resData['cart']['totalsum'] = $item['qty']*$item['itemPrice'];
                    isset($resData['cart']['totalweight']) ? $resData['cart']['totalweight'] += $item['qty']*$item['itemWeight']: $resData['cart']['totalweight'] = $item['qty']*$item['itemWeight'];
                }
                $resData['success'] = 1;
                $resData['message'] = 'Корзина успешно обновлена';
            }else{
                $resData['success'] = 0;
                $resData['message'] = 'Что-то пошло в Корзине при добавлении не так:((';
            }
        }else{
            $resData['success'] = 0;
            $resData['message'] = 'Что-то пошло в Корзине при удалении не так:((';
        }
        die(json_encode($resData));
    }



/*
 * оформление заказа клиента
 */
    public function orderAction(){
        $resData = array();
        foreach([   'name'  => ['value'=>$_POST['userInfo']['name'], 'message'=>'Поле ФИО заполнено неверно'],
                    'email' => ['value'=>$_POST['userInfo']['email'], 'message'=>'Поле email заполнено неверно'],
                    'adress' => ['value'=>$_POST['userInfo']['adress'], 'message'=>'Поле адрес заполнено неверно'],
                    'phone' => ['value'=>$_POST['userInfo']['phone'], 'message'=>'Поле телефон заполнено неверно'],
                    'note' => ['value'=>$_POST['userInfo']['note'], 'message'=>'Поле примечание заполнено неверно'],
                ] as $key=>$val){
            $res = Validator::$key($val['value'], $val['message']);
            if($res !== true){
                $resData['success'] = 0;
                $resData['message'] = $res;
                die(json_encode($resData));
            }
        }

        $shipping_info = $_POST['userInfo'];

        $order = $_POST['ItemsInOrder'];
        $products = [];
        foreach($order as $num => $item){
            $products[$num]['multiple_id'] = $item['multiple_id'];
            //$multiple_id = $item['multiple_id'];
            $id = explode('-', $products[$num]['multiple_id']);
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
                $product[$k]['multiple_id'] = $v['multiple_id'];
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['sum'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['product_info']['name'] = $product[$k]['name'];
                $product[$k]['product_info']['alias'] = $product[$k]['alias'];
                $product[$k]['product_info']['image'] = $product[$k]['image'];
                if(isset($product[$k]['prDepArr'])){
                    $product[$k]['product_info']['prDepArr'] = $product[$k]['prDepArr'];
                    unset($product[$k]['prDepArr']);
                }
                if(isset($product[$k]['prValArr'])){
                    $product[$k]['product_info']['prValArr'] = $product[$k]['prValArr'];
                    unset($product[$k]['prValArr']);
                }
                unset($product[$k]['name']);
                unset($product[$k]['alias']);
                unset($product[$k]['image']);
                $product[$k]['product_info'] = json_encode($product[$k]['product_info']);
            }elseif(!isset($v['pd'])){
                $product[] = $model->getOneProduct($v['product_id'], $v['pp'], '');
                $product[$k]['multiple_id'] = $v['multiple_id'];
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['sum'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['product_info']['name'] = $product[$k]['name'];
                $product[$k]['product_info']['alias'] = $product[$k]['alias'];
                $product[$k]['product_info']['image'] = $product[$k]['image'];
                if(isset($product[$k]['prDepArr'])){
                    $product[$k]['product_info']['prDepArr'] = $product[$k]['prDepArr'];
                    unset($product[$k]['prDepArr']);
                }
                if(isset($product[$k]['prValArr'])){
                    $product[$k]['product_info']['prValArr'] = $product[$k]['prValArr'];
                    unset($product[$k]['prValArr']);
                }
                unset($product[$k]['name']);
                unset($product[$k]['alias']);
                unset($product[$k]['image']);
                $product[$k]['product_info'] = json_encode($product[$k]['product_info']);
            }elseif(!isset($v['pp'])){
                $product[] = $model->getOneProduct($v['product_id'], [], $v['pd']);
                $product[$k]['multiple_id'] = $v['multiple_id'];
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['sum'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['product_info']['name'] = $product[$k]['name'];
                $product[$k]['product_info']['alias'] = $product[$k]['alias'];
                $product[$k]['product_info']['image'] = $product[$k]['image'];
                if(isset($product[$k]['prDepArr'])){
                    $product[$k]['product_info']['prDepArr'] = $product[$k]['prDepArr'];
                    unset($product[$k]['prDepArr']);
                }
                if(isset($product[$k]['prValArr'])){
                    $product[$k]['product_info']['prValArr'] = $product[$k]['prValArr'];
                    unset($product[$k]['prValArr']);
                }
                unset($product[$k]['name']);
                unset($product[$k]['alias']);
                unset($product[$k]['image']);
                $product[$k]['product_info'] = json_encode($product[$k]['product_info']);
            }else{
                $product[] = $model->getOneProduct($v['product_id'], $v['pp'], $v['pd']);
                $product[$k]['multiple_id'] = $v['multiple_id'];
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['sum'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['product_info']['name'] = $product[$k]['name'];
                $product[$k]['product_info']['alias'] = $product[$k]['alias'];
                $product[$k]['product_info']['image'] = $product[$k]['image'];
                if(isset($product[$k]['prDepArr'])){
                    $product[$k]['product_info']['prDepArr'] = $product[$k]['prDepArr'];
                    unset($product[$k]['prDepArr']);
                }
                if(isset($product[$k]['prValArr'])){
                    $product[$k]['product_info']['prValArr'] = $product[$k]['prValArr'];
                    unset($product[$k]['prValArr']);
                }
                unset($product[$k]['name']);
                unset($product[$k]['alias']);
                unset($product[$k]['image']);
                $product[$k]['product_info'] = json_encode($product[$k]['product_info']);
            }
        }
        $sum = array_sum(array_column($product, 'sum'));

        $user = User::isUser();
        if($user){
            $user_id = $user['id'];
        }else{
            $user_id = 0;
        }

        $data = [];
        $data['user_id'] = $user_id;
        $data['shipping_info'] = json_encode($shipping_info);
        $data['currency'] = json_encode($_SESSION['cart.currency']);
        $data['date_created'] = date("Y-m-d H:i:s");
        $data['date_payment'] = NULL;
        $data['sum'] = $sum;
        $data['status'] = '0';
        $data['note'] = '-';

        $model = new Cart();
        $order_id = $model->insertAndReturnId($table = 'orders', $data);

        $resData = [];
        if($order_id){
            $orders_items = [];
            foreach ($product as $num=>$order_item) {
                $orders_items[$num]['order_id'] = $order_id;
                $orders_items[$num]['order_date'] = $data['date_created'];
                $orders_items[$num]['user_id'] = $data['user_id'];
                $orders_items[$num]['product_id'] = $order_item['id'];
                $orders_items[$num]['multiple_id'] = $order_item['multiple_id'];
                $orders_items[$num]['product_info'] = $order_item['product_info'];
                $orders_items[$num]['qty'] = $order_item['qty'];
                $orders_items[$num]['price'] = $order_item['itemPrice'];
                $orders_items[$num]['note'] = 'принято';
            }
            $ins_items = $model->insertInTable($table = 'order_items', $orders_items);
            if($ins_items){
                if(isset($_SESSION['cart'])){
                    unset($_SESSION['cart']);
                }else if(isset($_SESSION['user']['cart'])){
                    unset($_SESSION['user']['cart']);
                    $model->deleteUserCart($user_id);
                }
                $resData['success'] = 1;
                $resData['message'] = 'Заказ принят, с Вами свяжется оператор в ближайшее время';
            }
        }else{
            $resData['error'] = 1;
            $resData['message'] = 'Заказ не может быть оформлен по техническим причинам, попробуйте позднее';
        }
        die(json_encode($resData));
    }

}