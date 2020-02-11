<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 07.02.2020
 * Time: 14:31
 */

namespace app\controllers\admin;
use app\models\admin\Property;
use im\core\App;
use im\core\base\View;

class PropertyController extends AppController{

    public function indexAction(){
        $model = new Property;

        $properties = $model->findAll();
        $propertiesValues = $model->findAll('properties_values');

        View::setMeta('Список характеристик', '', '');
        $this->set(compact('properties', 'propertiesValues'));
    }

    public function addAction(){
        $model = new Property;

        $properties = $model->findAll();
        //$propertiesValues = $model->findAll('properties_values');

        View::setMeta('Добавление характеристики', '', '');
        $this->set(compact('properties'));

        if(!empty($_POST)){
            $data = $_POST;
            $res = [];
            $data['name'] = mb_ucfirst($data['name']);
            if(isset($data['name'])){
                $count = $model->selectCountFromTable('properties', 'name', $data['name']);
                if($count == 0){
                    $property_id = $model->insertAndReturnId('properties', $data);
                    if(!$property_id){
                        $res['success'] = 0;
                        $res['message'] = 'Сбой при добавлении характеристики';
                        echo json_encode($res);
                        die;
                    }
                    $res['id'] = $property_id;
                    $res['success'] = 1;
                    $res['message'] = 'Характеристика успешно добавлена';
                }else{
                    $res['success'] = 0;
                    $res['message'] = 'Такая характеристика уже имеется';
                }
                echo json_encode($res);
                die;
            }
        }
    }

    public function addpropvalAction(){
        $model = new Property;

        $properties = $model->findAll();
        //$propertiesValues = $model->findAll('properties_values');

        View::setMeta('Добавить значение характеристики', '', '');
        $this->set(compact('properties'));

        if(!empty($_POST)){
            $data = $_POST;
            $res = [];
            if(isset($data['name'])){
                $count = $model->selectCountFromTable('properties_values', 'name', $data['name']);
                $count_val = $model->selectCountFromTable('properties_values', 'value', $data['value']);
                if(($count == 0) && ($count_val == 0)){
                    $mydata = [];
                    $mydata[] = $data;
                    $property_id = $model->insertInTable('properties_values', $mydata);
                    if(!$property_id){
                        $res['success'] = 0;
                        $res['message'] = 'Сбой при добавлении данных';
                        echo json_encode($res);
                        die;
                    }
                    $res['success'] = 1;
                    $res['message'] = 'Значение характеристики успешно добавлено';
                }else{
                    $res['success'] = 0;
                    $res['message'] = 'Такое значение уже имеется';
                }
                echo json_encode($res);
                die;
            }
        }
    }

    public function getValuesAction(){
        $model = new Property;
        $res = [];
        if(!empty($_POST)){
            $data = $_POST;
            $propertiesValues = $model->findEqual($data['id'], 'property_id' ,'properties_values');
            if(!$propertiesValues){
                $res['success'] = 0;
                $res['message'] = 'Сбой при выборке значений';
                echo json_encode($res);
                die;
            }
            $res['values'] = $propertiesValues;
            $res['success'] = 1;
        }else{
            $res['values'] = [];
            $res['success'] = 1;
        }
        echo json_encode($res);
        die;
    }

}

/*$propertiesValues = $model->findAll('properties_values');
foreach($propertiesValues as $k=>$v){
    if($property_id==$v['property_id']){
        $res['property_values'][] = $v['name'];
    }
}*/