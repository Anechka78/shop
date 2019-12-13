<?php

namespace app\models;

use im\core\App;
use im\core\base\Model;

class Cart extends Model{

    /*
     * Получаем из сессии данные об итоговых суммах корзины пользователя totalqty, totalsum, totalweight
     */
    public function addToUsersSessionCart($price, $weight){
        if(isset($_SESSION['user']['cart'])){
            $_SESSION['user']['cart']['totalsum'] = $_SESSION['user']['cart']['totalsum'] + $price;
            $_SESSION['user']['cart']['totalqty'] = $_SESSION['user']['cart']['totalqty'] + 1;
            $_SESSION['user']['cart']['totalweight'] = $_SESSION['user']['cart']['totalweight'] + $weight;
        }else{
            $_SESSION['user']['cart']['totalsum'] = $price;
            $_SESSION['user']['cart']['totalqty'] = 1;
            $_SESSION['user']['cart']['totalweight'] = $weight;
        }
        return($_SESSION['user']['cart']);
    }

    /*
     * Получаем из базы данные сведения о товарах в корзине зарегистрированного пользователя
     */
    public function getUsersCart($user_id){
        $ids = $this->findBySql("SELECT `product_id`, `count` FROM `users_cart` WHERE `user_id` = ?", [$user_id]);

        $products = [];
        foreach($ids as $k=>$val){
            $multiple_id = $val['product_id'];
            $id = explode('-', $multiple_id);
            $products[$multiple_id]['qty'] = $val['count'];
            $products[$multiple_id]['product_id'] = $id[0];
            $q = count($id);
            for($i=1; $i<$q; $i++){
                if(substr($id[$i], 0, 2) == 'pp'){
                    $products[$multiple_id]['pp'][] = substr($id[$i], 2);
                }elseif(substr($id[$i], 0, 2) == 'pd'){
                    $products[$multiple_id]['pd'] = substr($id[$i], 2);
                }
            }
        }

        $model = new Product();
        $product = [];
        foreach($products as $k=>$v){
            if((!isset($v['pp'])) && (!isset($v['pd']))){
                $product[$k] = $model->getOneProduct($v['product_id']);
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['summ'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['weight'] = $v['qty']*$product[$k]['itemWeight'];
            }elseif(!isset($v['pd'])){
                $product[$k] = $model->getOneProduct($v['product_id'], $v['pp'], '');
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['summ'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['weight'] = $v['qty']*$product[$k]['itemWeight'];
            }elseif(!isset($v['pp'])){
                $product[$k] = $model->getOneProduct($v['product_id'], [], $v['pd']);
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['summ'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['weight'] = $v['qty']*$product[$k]['itemWeight'];
            }else{
                $product[$k] = $model->getOneProduct($v['product_id'], $v['pp'], $v['pd']);
                $product[$k]['qty'] = $v['qty'];
                $product[$k]['summ'] = $v['qty']*$product[$k]['itemPrice'];
                $product[$k]['weight'] = $v['qty']*$product[$k]['itemWeight'];
            }
        }

        foreach($product as $prod_id=>$prod_info){
            if($prod_info['itemCount'] == 0){
                unset($product[$prod_id]);
                $this->deleteItemFromTable('users_cart', $product[$prod_id], 'product_id', $user_id, 'user_id');
            }elseif($prod_info['qty'] > $prod_info['itemCount']){
                $product[$prod_id]['qty'] = $prod_info['itemCount'];
            }
        }
//debug($product); die();
        $cart = $this->getTotalForUsersCart($product);
        return $cart;
    }

/*
 * Открываем страницу Корзины для неавторизованного пользователя
 */
    public function getCart(){
        $itemsArr = isset($_SESSION['cart']['products']) ? $_SESSION['cart']['products'] : array(); //получаем список продуктов, находящихся в корзине

        $currency = isset($_SESSION['cart.currency']) ? $_SESSION['cart.currency'] : array();

        $qty = isset($_SESSION['cart']['totalqty']) ? $_SESSION['cart']['totalqty'] : array();
        $sum = isset($_SESSION['cart']['totalsum']) ? $_SESSION['cart']['totalsum'] : array();
        $weight = isset($_SESSION['cart']['totalweight']) ? $_SESSION['cart']['totalweight'] : array();

        $res = [];
        $res['itemsArr'] = $itemsArr;
        $res['currency'] = $currency;
        $res['qty'] = $qty;
        $res['sum'] = $sum;
        $res['weight'] = $weight;
        return($res);
    }
/*
 * Добавление товара в Корзину авторизованного пользователя
 */

  public function addToUserCart($user_id, $ID, $product, $qty = 1)
  {
      $jsonproduct = json_encode($product);
      //debug($jsonproduct); die();
      //Проверяем, есть ли в БД товар с данным составным ID и берем кол-во ранее заказанных пользователем товаров
      $issetItem = $this->selectQtyFromUserCart($user_id, $ID);

      if (!$issetItem) {
          //если нет - записываем товар в БД таблицу корзины пользователя
          return $this->insertInUserCart($user_id, $ID, $jsonproduct, $qty = 1);
      } else {
          //если есть - получаем кол-во товаров из БД и добавляем еще один
          $qty = $issetItem[0]['count'] + 1;
          if($qty > $product['itemCount']){
              return false;
          }else{
              return $this->updateUserCart($qty, $user_id, $ID);
          }
      }
  }
/*
 * Добавление товара в Корзину авторизованного пользователя при конфликте Корзин
 */
    public function addToUsersCartDiff($user_id, $products){
        foreach($products as $prod_id=>$product){
            $sql = "INSERT INTO `users_cart` (`user_id`, `product_id`, `json_product`, `count`, `cart_data`) VALUES (?, ?, ?, ?, NOW())";
            $res = $this->findBySql($sql, [$user_id, $prod_id, json_encode($product), $product['qty']]);
        }
        unset($_SESSION['cart']);
        return true;
    }


//    //Получаем корзину пользователя
//      $dbCart = $this->findEqual($user_id, 'user_id', 'users_cart');
//      //debug($dbCart); die();
//      $products=[];
//
//      //заполняем массив продуктов
//      foreach($dbCart as $key=>$item){
//          $id = $item['product_id'];
//          $item['json_product'] = json_decode($item['json_product'], true);
//          //$item['count'] - это кол-во заказанного товара, блин, а не его остаток на складе
//          //debug($item);
//          $products[$id]['qty'] = $item['count'];
//          $products[$id]['product'] = $item['json_product'];
//          $products[$id]['price'] = $item['json_product']['itemPrice'];
//          $products[$id]['weight'] = $item['json_product']['itemWeight'];
//          $products[$id]['count'] = $item['json_product']['itemCount'];
//          $products[$id]['summ'] = $products[$id]['price'] * $products[$id]['qty'];
//      }
//     // debug($products); die();
//
//      //Проверяем, есть ли товар в наличии, если нет - удаляем из корзины
//      //Если же кол-во товара меньше, чем заказанное - делаем кол-во товара в корзине равным тому, что в наличии
//      foreach($products as $id=>$pr) {
//          if($pr['count'] == 0) {
//              $this->deleteItemFromTable('users_cart', $id, 'product_id', $user_id, 'user_id');
//              unset($products[$id]);
//          }else if($pr['qty'] > $pr['count']){
//              $products[$id]['qty'] = $products[$id]['count'];
//          }
//      }
//      $totalWeight =  array_sum(array_column($products, 'weight'));
//      $totalSum = array_sum(array_column($products, 'summ'));
//      $totalQty = array_sum(array_column($products, 'qty'));
//      $cart = [];
//      $cart['products'] = $products;
//      $cart['totalsum'] = $totalSum;
//      $cart['totalqty'] = $totalQty;
//      $cart['totalweight'] = $totalWeight;
//
//      $_SESSION['user']['cart']['totalsum'] = $totalSum;
//      $_SESSION['user']['cart']['totalqty'] = $totalQty;
//      return $cart;
//  }

/*
 * Добавление товара в Корзину незарегистрированного пользователя
 */
    public function addToCart($product, $qty = 1, $ID){
        //debug($product); die();
        if(!isset($_SESSION['cart.currency'])){
            $_SESSION['cart.currency'] = App::$app->getProperty('currency');
        }

//        /* ФОРМИРУЕМ СОСТАВНОЙ (уникальный) ID ТОВАРА*/
//        $ID = $product['id'];
//        if(isset($product['prDepArr'])){
//            $ID .= '-pd'.$product['prDepArr']['id'];
//        }
//        if(isset($product['prValArr'])){
//            foreach($product['prValArr'] as $key=>$val){
//                $ID .= '-pp'.$val['id'];
//            }
//        }

//        /*Функция, которая принимает на вход массивы и определяет по логике, что главней и находит данные*/
//        function getValueFromArrays($product, $value = ''){
//            $myvalue = $product[$value];
//            if(isset($product['prValArr'])){
//                foreach($product['prValArr'] as $key=>$val){
//                    if($val[$value] != 0){
//                        $myvalue = $val[$value];
//                    }
//                }
//            }
//            if(isset($product['prDepArr'])){
//                if($product['prDepArr'][$value] != 0){
//                    $myvalue = $product['prDepArr'][$value];
//                }
//            }
//            return $myvalue;
//
//        }

        /* ОПРЕДЕЛЯЕМ ЦЕНУ ТОВАРА (в зависимости от наличия цены в характеристиках) и курса текущей валюты*/
        $price = $product['itemPrice'];
        $weight = $product['itemWeight'];
        $count = $product['itemCount'];

        /* Если в Корзине уже существует такой товар*/
        if(isset($_SESSION['cart']['products'][$ID])){
            if($_SESSION['cart']['products'][$ID]['qty'] + $qty > $_SESSION['cart']['products'][$ID]['count']){
                return false;
            }else{
                $_SESSION['cart']['products'][$ID]['qty'] += $qty;
                $_SESSION['cart']['products'][$ID]['summ'] = $_SESSION['cart']['products'][$ID]['summ'] + $price *$qty;
            }
        }else{
            $_SESSION['cart']['products'][$ID] = $product;
            $_SESSION['cart']['products'][$ID]['qty'] = $qty;



//                [
//                'qty' => $qty,
//                //'product' => $product,
//                'itemPrice' => $price,
//                'itemSumm' => $price * $qty,
//                'itemCount' => $count,
//                'itemWeight' => $weight,
//            ];
        }

        // Добавляем в переменные корзины итоговую сумму и кол-во товаров в корзине, плюс вес заказа
        $_SESSION['cart']['totalweight'] = isset($_SESSION['cart']['totalweight']) ? $_SESSION['cart']['totalweight']+ $weight : $weight;
        $_SESSION['cart']['totalqty'] = isset($_SESSION['cart']['totalqty']) ? $_SESSION['cart']['totalqty']+ $qty : $qty;
        $_SESSION['cart']['totalsum'] = isset($_SESSION['cart']['totalsum']) ?
                                              $_SESSION['cart']['totalsum'] + $qty * $price :
                                              $qty * $price;

        return $_SESSION['cart'];
    }
/*
 * Изменение кол-ва товара в Коризне авторизованного пользователя
 */
    public function changeUsersCart($id, $qty, $user_id){
        $sql = "SELECT * FROM `users_cart` WHERE `user_id` = ? AND `product_id` = ? LIMIT 1";
        $item = $this->findBySql($sql, [$user_id, $id]);

        if(!$item){
            $resData['error'] = 1;
            $resData['message'] = 'Товара с таким id не найдено, повторите попытку позже';
            return($resData);
        }

        $item[0]['json_product'] = json_decode($item[0]['json_product'], true);
        $compare = $this->compare($qty, $item[0]['json_product']['itemCount']);

        if($compare == 0 || $compare == -1){
            $deltaQty = $qty - $item[0]['count'];
            $deltaSum = $deltaQty*$item[0]['json_product']['itemPrice'];
            $deltaWeight = $deltaQty*$item[0]['json_product']['itemWeight'];

            $item[0]['count'] = $qty;
            //Заносим новое кол-во товара в БД
            $this->updateUserCart($qty, $user_id, $id);

            //записываем в сессию данные для пользовательской мини-корзины
            $_SESSION['user']['cart']['totalsum'] = $_SESSION['user']['cart']['totalsum'] + $deltaSum;
            $_SESSION['user']['cart']['totalqty'] = $_SESSION['user']['cart']['totalqty'] + $deltaQty;
            $_SESSION['user']['cart']['totalweight'] = $_SESSION['user']['cart']['totalweight'] + $deltaWeight;

            $cart = [];
            $cart['totalsum'] = $_SESSION['user']['cart']['totalsum'];
            $cart['totalqty'] = $_SESSION['user']['cart']['totalqty'];
            $cart['totalweight'] = $_SESSION['user']['cart']['totalweight'];

            //Если корзина пуста - удаляем сессию корзины и валюты
            if ($cart['totalqty'] == 0) {
                unset($_SESSION['cart.currency']);
                unset($_SESSION['user']['cart']);
                $resData['success'] = 1;
                $resData['message'] = 'В корзине товаров не осталось и мы ее очистили';
            } else{
                $resData['success'] = 1;
                $resData['message'] = 'Количество товара было успешно изменено';
                $resData['cart'] = $cart;
            }
        }else{
            $resData['error'] = 0;
            $resData['message'] = 'Товара не может быть заказано больше, чем есть в наличии';
        }
        return($resData);
    }

/*
 * Изменение кол-ва товара в Коризне
 */
    public function changeCart($id, $qty){
        //debug($_SESSION); die();
        $products = $_SESSION['cart']['products'];
        $equalKey = false;
        foreach($products as $key=>$val){
            if($key == $id) {
                $resData = [];
                $equalKey = true;

                if ($qty > 0) {
                    if($qty > $val['itemCount']){
                        $resData['error'] = 0;
                        $resData['message'] = 'Товара не может быть заказано больше, чем есть в наличии';
                    }else{
                        //Меняем в сессии кол-во товара и сумму по нему
                        $_SESSION['cart']['products'][$key]['qty'] = $qty;
                        $_SESSION['cart']['products'][$key]['summ'] = $qty * $val['itemPrice'];

                        //Считаем общее кол-во товаров в корзине и их сумму
                        $totalQty = array_sum(array_column($_SESSION['cart']['products'], 'qty'));
                        $totalSum = array_sum(array_column($_SESSION['cart']['products'], 'summ'));
                        $totalWeight = array_sum(array_column($_SESSION['cart']['products'], 'weight'));

                        //Записываем полученные данные в сессионные переменные
                        $_SESSION['cart']['totalqty'] = $totalQty;
                        $_SESSION['cart']['totalsum'] = $totalSum;
                        $_SESSION['cart']['totalweight'] = $totalWeight;

                        //Передаем корзину в контроллер
                        $resData['success'] = 1;
                        $resData['message'] = 'Количество товара было успешно изменено';
                        $resData['cart'] = $_SESSION['cart'];
                    }
                } else if ($qty == 0) {
                    //удаляем из корзины данный товар
                    unset($_SESSION['cart']['products'][$key]);

                    //Считаем общее кол-во товаров в корзине и их сумму
                    $totalQty = array_sum(array_column($_SESSION['cart']['products'], 'qty'));
                    $totalSum = array_sum(array_column($_SESSION['cart']['products'], 'summ'));
                    $totalWeight = array_sum(array_column($_SESSION['cart']['products'], 'weight'));

                    //Записываем полученные данные в сессионные переменные
                    $_SESSION['cart']['totalqty'] = $totalQty;
                    $_SESSION['cart']['totalsum'] = $totalSum;
                    $_SESSION['cart']['totalweight'] = $totalWeight;

                    //Если корзина пуста - удаляем сессию корзины и валюты
                    if ($totalQty == 0) {
                        unset($_SESSION['cart']);
                        unset($_SESSION['cart.currency']);
                        $resData['success'] = 1;
                        $resData['message'] = 'В корзине товаров не осталось и мы ее очистили';
                    } else {
                        //Если товары есть - передаем Корзину в контроллер
                        $resData['success'] = 1;
                        $resData['message'] = 'Количество товара было успешно изменено';
                        $resData['cart'] = $_SESSION['cart'];
                    }
                } else {
                    $resData['error'] = 0;
                    $resData['message'] = 'Что-то пошло не так, повторите попытку позже';
                }
            }
        }
        if (!$equalKey){
            $resData['error'] = 1;
            $resData['message'] = 'Товара с таким id не найдено, повторите попытку позже';
        }
        return($resData);
    }
/*
 * Удаление товара из корзины авторизованного пользователя
 */
    public function removeFromUsersCart($user_id, $itemToDel){
        $this->deleteItemFromTable('users_cart', $itemToDel, 'product_id', $user_id, 'user_id');
        return true;
    }

/*
 * Удаление товара из корзины
 */
    public function removeFromCart($itemToDel){
        if(array_key_exists($itemToDel, $_SESSION['cart']['products'])){
            $deltaqty = $_SESSION['cart']['products'][$itemToDel]['qty'];
            $deltasum = $_SESSION['cart']['products'][$itemToDel]['qty']*$_SESSION['cart']['products'][$itemToDel]['itemPrice'];
            $deltaweight = $_SESSION['cart']['products'][$itemToDel]['qty']*$_SESSION['cart']['products'][$itemToDel]['itemWeight'];

            unset($_SESSION['cart']['products'][$itemToDel]); // с помощью ф-ии unset удаляем такой элемент из массива

//            $totalQty = array_sum(array_column($_SESSION['cart']['products'], 'qty'));
//            $totalSum = array_sum(array_column($_SESSION['cart']['products'], 'summ'));
//            $totalWeight = array_sum(array_column($_SESSION['cart']['products'], 'weight'));

            $_SESSION['cart']['totalqty'] = $_SESSION['cart']['totalqty'] - $deltaqty;
            $_SESSION['cart']['totalsum'] = $_SESSION['cart']['totalsum'] - $deltasum;
            $_SESSION['cart']['totalweight'] = $_SESSION['cart']['totalweight'] - $deltaweight;
        }
        return $_SESSION['cart'];
    }

/*
 * Очистка корзины неавторизованного пользователя
 */
    public function deleteCart(){
        unset($_SESSION['cart']);
        unset($_SESSION['cart.currency']);
        if(!isset($_SESSION['user']['cart']) && !isset($_SESSION['cart.currency'])){
            return 1;
        } else{
            return 0;
        }
    }

/*
 * Очистка корзины авторизованного пользователя
 */
    public function deleteUserCart($user_id){
        $ids = $this->findBySql("DELETE FROM `users_cart` WHERE `user_id` = ?", [$user_id]);
        unset($_SESSION['user']['cart']);
        unset($_SESSION['cart.currency']);
        if(!isset($_SESSION['user']['cart']) && !isset($_SESSION['cart.currency'])){
            return 1;
        } else{
            return 0;
        }
    }

/*
 * Изменения в Корзине при смене валюты
 */

    public static function recalc($curr){
        if(isset($_SESSION['cart.currency'])){

            foreach($curr as $key=>$value){
                $_SESSION['cart.currency'][$key] = $value;
            }
        }
    }

    public function insertInUserCart($user_id, $ID, $jsonproduct, $qty = 1){
        $sql = "INSERT INTO `users_cart` (`user_id`, `product_id`, `json_product`, `count`, `cart_data`) VALUES (?, ?, ?, ?, NOW())";
        $res = $this->findBySql($sql, [$user_id, $ID, $jsonproduct, $qty]);
        return true;
    }

    public function selectQtyFromUserCart($user_id, $ID){
        $sql = "SELECT `count` FROM `users_cart` WHERE `user_id` = ? AND `product_id` = ? LIMIT 1";
        $item = $this->findBySql($sql, [$user_id, $ID]);
        return $item;
    }

    public function updateUserCart($qty, $user_id, $ID){
        if($qty == 0){
            //удаляем из корзины в БД данный товар
            $this->deleteItemFromTable('users_cart', $ID, 'product_id', $user_id, 'user_id');
        }else{
            $sql = "UPDATE `users_cart` SET `count` = ? WHERE `user_id` = ? AND `product_id` = ?";
            $this->findBySql($sql, [$qty, $user_id, $ID]);
        }
        return true;
    }


    /*Функция, которая принимает на вход массивы и определяет по логике, что главней и находит данные без сверки с БД*/
    public static function getValueFromArray($product, $value = ''){
        $myvalue = $product[$value];
        if(isset($product['prValArr'])){
            foreach($product['prValArr'] as $key=>$val){
                if($val[$value] != 0){
                    $myvalue = $val[$value];
                }
            }
        }
        if(isset($product['prDepArr'])){
            if($product['prDepArr'][$value] != 0){
                $myvalue = $product['prDepArr'][$value];
            }
        }
        return $myvalue;
    }

    public static function getTotalForUsersCart($products){
    //Считаем общее кол-во товаров в корзине и их сумму
        $totalQty = array_sum(array_column($products, 'qty'));
        $totalSum  = array_sum(array_column($products, 'summ'));
        $totalWeight = array_sum(array_column($products, 'weight'));

        $cart = [];
        $cart['products'] = $products;
        $cart['totalsum'] = $totalSum;
        $cart['totalqty'] = $totalQty;
        $cart['totalweight'] = $totalWeight;

        //записываем в сессию данные для пользовательской мини-корзины
        $_SESSION['user']['cart']['totalsum'] = $totalSum;
        $_SESSION['user']['cart']['totalqty'] = $totalQty;
        $_SESSION['user']['cart']['totalweight'] = $totalWeight;

        return $cart;
    }


}