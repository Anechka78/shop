<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 15.11.2018
 * Time: 14:37
 */

namespace app\controllers;

use app\models\Cart;
use app\models\User;
use im\core\App;
use im\core\base\View;

class UserController extends AppController
{
    public function signupAction(){
        //die('qqqqqqqqqqqqqqqqqqqqqqqq');
        if(!empty($_POST)){
            $user = new User();            //debug($user);
            $data = $_POST;
            $user->load($data);            //debug($user); die('qqqqqqqqqqqqqqqqqqqqqqqq');           //debug($_POST);
            $resData = [];
            if(!$user->validate($data) || !$user->checkUnique()){
                $user->getErrors();
                $_SESSION['form_data'] = $data;//записываем в сессию данные, чтобы пользователь при ошибке их 100 раз не вводил
                $resData['success'] = 0;
                $resData['message'] = $_SESSION['error'];
                die(json_encode($resData));
            }
            $user->attributes['pwd1'] = password_hash(trim($user->attributes['pwd1']), PASSWORD_DEFAULT);

            if($user->save()){
                $_SESSION['success'] = 'Вы успешно зарегистрировались!';
                $resData['user'] = $user;
                $resData['success'] = 1;
                $resData['message'] = $_SESSION['success'];
                die(json_encode($resData));
                }
            }else{
                $_SESSION['error'] = 'Ошибка! Попробуйте позже.';
                $resData['success'] = 0;
                $resData['message'] = $_SESSION['error'];
                die(json_encode($resData));
            }
            //redirect();

        //View::setMeta('Регистрация');
    }

    public function loginAction(){
        if(!empty($_POST)){
            //var_dump($_POST); die();
            $user = new User();
            $resData = [];
            if($user->login()){
                //var_dump($_SESSION['user']); die('**********************');
                $currency = isset($_SESSION['cart.currency']) ? $_SESSION['cart.currency'] : $_SESSION['cart.currency'] = App::$app->getProperty('currency');
                $_SESSION['success'] = 'Вы успешно авторизованы';
                $resData['success'] = 1;
                $resData['message'] = $_SESSION['success'];
                $resData['user'] = $_SESSION['user'];

                $user_id = $_SESSION['user']['id'];

                $userCart = new Cart();
                $cart = $userCart->getUsersCart($user_id);


            }else{
                $_SESSION['error'] = 'Логин/пароль введены неверно';
                $resData['success'] = 0;
                $resData['message'] = $_SESSION['error'];
            }
            die(json_encode($resData));
        }
       // View::setMeta('Вход');
    }

    public function logoutAction(){
        if(isset($_SESSION['user'])) unset($_SESSION['user']);
        redirect('');
    }

    public function isUser(){
        if(isset($_SESSION['user'])){
            return ($_SESSION['user']);
        }else{
            return false;
        }
    }
}