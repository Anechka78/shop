<?php

namespace app\models;

use im\core\base\Model;

class User extends Model
{
    public $attributes = [
        'login' => '',
        'address' => '',
        'name' => '',
        'phone' => '',
        'email' => '',
        'pwd1' => '',
        'role' => 'user',
    ];

    //Валидатор - задаем правила, которым должны соответвовать поля
    public $rules = [
        'required' => [
            //['login'],
            ['email'],
            ['pwd1'],
            //['name'],
        ],
        'email' => [
            ['email'],
        ],
        'lengthMin' => [
            ['pwd1', 6],
        ],
    ];

    public $table = 'userss';

    public function checkUnique(){
        //$sql = "SELECT * FROM {$this->table} WHERE login = ? OR email = ? LIMIT 1";
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $user = $this->pdo->query($sql, [$this->attributes['email']]);
        //var_dump($user[0]['login']); die;
        if($user){
            /*if($user[0]['login'] == $this->attributes['login']){
                $this->errors['unique'][] = 'Этот логин уже занят';
            }*/
            if($user[0]['email'] == $this->attributes['email']){
                $this->errors['unique'][] = 'Этот email уже зарегистрирован в системе';
            }
            return false;
        }
        return true;
    }

    /**
     * Проверка входа пользователя на сайт
     * $_POST['login'] и $_POST['password'] - данные, передаваемые из формы login.php
     */
    public function login($isAdmin = false){
        $login = !empty(trim($_POST['email'])) ? trim($_POST['email']) : null;
        $password = !empty(trim($_POST['pwd'])) ? trim($_POST['pwd']) : null;
        var_dump($_POST);
        //var_dump($login.'  '.$password);

        if($login && $password){
            if($isAdmin){
                $sql = "SELECT * FROM {$this->table} WHERE email = ? AND role = 'admin' LIMIT 1";
                $user = $this->pdo->query($sql, [$login]);
            } else{
                $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
                $user = $this->pdo->query($sql, [$login]);
            }

            if($user){
                //var_dump('++++++++++'); die();
                if(password_verify($password, $user[0]['pwd1'])){
                    foreach($user[0] as $key=>$value){
                        if($key != 'pwd1'){
                            $_SESSION['user'][$key] = $value;
                        }
                        //var_dump($_SESSION['user']); die();
                    }
                    if($_SESSION['user']['login'] == ''){
                        $_SESSION['user']['login'] = $_SESSION['user']['email'];
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Проверка, авторизован пользователь или нет
     * @return bool
     */
    public static function isUser(){
        if(isset($_SESSION['user'])){
            return($_SESSION['user']);
        }else{
            return false;
        }
    }

    /**
     * Проверка, является ли пользователь администратором сайта
     * @return bool
     */
    public static function isAdmin(){
        return(isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin');
    }


}