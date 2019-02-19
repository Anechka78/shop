<?php

namespace app\controllers\admin;

use app\models\User;
use im\core\base\View;
use im\libs\Pagination;

class UserController extends AppController{

    public function indexAction(){
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perpage = 3;
        $model = new User();
        $count = $model->selectCount('userss');
        $pagination = new Pagination($page, $perpage, $count);
        $start = $pagination->getStart();
        $modeladmin = new \app\models\admin\User();
        $users = $modeladmin->getAllUsers($start, $perpage);

        View::setMeta('Список пользователей', '', '');
        $this->set(compact('users', 'pagination', 'count'));
    }

    public function addAction(){
        View::setMeta('Добавление пользователя', '', '');
    }

    public function editAction(){
        if(!empty($_POST)){
            $id = $this->getRequestID(false);
            $user = new \app\models\admin\User();
            $data = $_POST;
            //debug($data); die();
            $user->load($data);
            if(!$user->attributes['pwd1']){
                unset($user->attributes['pwd1']);
                unset($data['pwd1']);
            }else{
                $data['pwd1'] = password_hash($user->attributes['pwd1'], PASSWORD_DEFAULT);
            }
            if(!$user->validate($data) || !$user->checkUnique()){
                $user->getErrors();
                redirect();
            }
            if($user->updateTable('userss', $data, 'id', $id)){
                $_SESSION['success'] = 'Изменения сохранены';
            }
            redirect();
        }

        $user_id = $this->getRequestID();

        $model = new User();
        $user = $model->findOne($user_id, 'id', 'userss')[0];

        $modeladmin = new \app\models\admin\User();
        $orders = $modeladmin->getUserOrders($user_id);


        View::setMeta('Редактирование профиля пользователя', '', '');
        $this->set(compact('user'));
    }

    public function loginAdminAction(){
        $this->layout = 'login';
        View::setMeta('Авторизация', '', '');

        if(!empty($_POST)){
            $user = new User();
            if($user->login(true)){
                $_SESSION['success'] = 'Вы успешно авторизованы';
            }else{
                $_SESSION['error'] = 'Логин/пароль введены неверно';
            }
            if(User::isAdmin()){
                redirect(ADMIN);
            }else{
                redirect();
            }
        }

    }

    public function signupAction(){
        if(!empty($_POST)){
            $user = new User();            //debug($user);
            $data = $_POST;
            $user->load($data);            //debug($user); die('qqqqqqqqqqqqqqqqqqqqqqqq');           //debug($_POST);
            $resData = [];
            if(!$user->validate($data) || !$user->checkUnique()){
                $user->getErrors();

                $_SESSION['form_data'] = $data;//записываем в сессию данные, чтобы пользователь при ошибке их 100 раз не вводил
                //$_SESSION['errors'] = 'Данные указаны неверно. Возможно, этот email уже есть в системе';

                redirect();
            }
            $user->attributes['pwd1'] = password_hash(trim($user->attributes['pwd1']), PASSWORD_DEFAULT);

            if($user->save()){
                $_SESSION['success'] = 'Вы успешно зарегистрировались!';

            }
        }else{
            $_SESSION['error'] = 'Ошибка! Попробуйте позже.';

        }
        redirect();
    }
}