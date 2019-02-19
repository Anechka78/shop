<?php

namespace app\models\admin;



class User extends \app\models\User{

    public $attributes = [
        'id' => '',
        'email' => '',
        'pwd1' => '',
        'role' => 'user',
    ];

    //Валидатор - задаем правила, которым должны соответвовать поля
    public $rules = [
        'required' => [
            ['email'],
            ['role'],
        ],
        'email' => [
            ['email'],
        ],
    ];

    public $table = 'userss';

    public function checkUnique(){
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND id != ? LIMIT 1";
        $user = $this->pdo->query($sql, [$this->attributes['email'], $this->attributes['id']]);

        if($user){
            if($user[0]['email'] == $this->attributes['email']){
                $this->errors['unique'][] = 'Этот email уже зарегистрирован в системе';
            }
            return false;
        }
        return true;
    }

    public function getAllUsers($start, $perpage){
        $sql = "SELECT * FROM `userss` LIMIT :start, :perpage";
        $users = $this->findBySql($sql, array('start' => $start, 'perpage' => $perpage));
        return $users;
    }

    public function getUserOrders($user_id){

    }

}