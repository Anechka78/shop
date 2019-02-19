<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 14.11.2018
 * Time: 11:22
 */

namespace app\controllers\admin;

use app\models\Main;
use app\models\User;
use im\core\base\Controller;

class AppController extends Controller
{
    public $layout = 'admin';
    public function __construct($route){
        parent::__construct($route);

        if(!User::isAdmin() && $route['action'] != 'login-admin'){
            redirect(ADMIN.'/user/login-admin');
        }
        //$model = new Main();
    }
    public function getRequestID($get = true, $id = 'id'){
        if($get){
            $data = $_GET;
        }else{
            $data = $_POST;
        }
        $id = !empty($data[$id]) ? (int)$data[$id] : null;
        if(!$id){
            return false;
        }
        return $id;
    }

}