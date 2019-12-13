<?php

namespace app\controllers\admin;


use app\models\admin\Category;
use app\models\admin\Model;
use im\core\App;
use im\core\base\View;
use im\core\Validator;

class CategoryController extends AppController
{
    public function indexAction(){
        View::setMeta('Список категорий', '', '');
    }

    /**
     * Удаление категории
     */
    public function deleteAction(){
        $id = $this->getRequestID();
        $model = new Category();
        $children = $model->selectCountChildren('categories', 'parent_id', $id);
        $errors = '';
        if($children){
            $errors .= '<li>Удаление невозможно, в категории есть потомки</li>';
        }
        $products = $model->selectCountChildren('products', 'category_id', $id);
        if($products){
            $errors .= '<li>Удаление невозможно, в категории есть товары</li>';
        }
        if($errors){
            $_SESSION['error'] = "<ul>$errors</ul>";
            redirect();
        }
        $del = $model->deleteItemFromTable('categories', '', '', $id, 'id');
        $_SESSION['success'] = 'Категория успешно удалена';
        redirect();
    }

    /**
     * Добавление категории
     */
    public function addAction(){
        View::setMeta('Добавить категорию', '', '');
        if(!empty($_POST)){
            $category = new Category();

            $errors = '';
            $success = '';
            //валидируем пришедшие данные
            foreach([   'word'  => ['value'=>$_POST['name'], 'message'=>'Наименование категории заполнено неверно'],
                        'note' => ['value'=>$_POST['description'], 'message'=>'Описание категории заполнено неверно'],
                    ] as $key=>$val){
                $res = Validator::$key($val['value'], $val['message']);
                if($res !== true){
                    $errors .= '<li>'.$res.'</li>';
                    $_SESSION['error'] = "<ul>$errors</ul>";
                    redirect();
                }
            }
            $data = $_POST;
            $name = $data['name'];
            //Проверяем, есть ли уже в БД такое название категории
            $qty = $category->selectCountFromTable('categories', 'name', $name);
            if($qty){
                $errors .= '<li>Добавление невозможно, такая категория уже имеется</li>';
            }
            if($errors){
                $_SESSION['error'] = "<ul>$errors</ul>";
                redirect();
            }

            //Создаем алиас на основе названия категории
            $data['alias'] = Model::str2url($name);
            //Записываем категорию в БД и получаем id записи
            $id = $category->insertAndReturnId('categories', $data);
            //Создаем новую таблицу для фильтров
            $res = $category->createFilterCategory($id);

            if($res){
                $success = '<li class="success">Категория добавлена успешно</li>';
                $_SESSION['success'] = "<ul>$success</ul>";
            }
            redirect();
        }
    }

    /**
     * Редактирование категории
     */
    public function editAction(){
        if(!empty($_POST)){
            $id = $this->getRequestID(false);

            $errors = '';
            $success = '';
            //валидируем пришедшие данные
            foreach([   'word'  => ['value'=>$_POST['name'], 'message'=>'Наименование категории заполнено неверно'],
                        'note' => ['value'=>$_POST['description'], 'message'=>'Описание категории заполнено неверно'],
                    ] as $key=>$val){
                $res = Validator::$key($val['value'], $val['message']);
                if($res !== true){
                    $errors .= '<li>'.$res.'</li>';
                    $_SESSION['error'] = "<ul>$errors</ul>";
                    redirect();
                }
            }
            $data = $_POST;
            //Создаем алиас на основе названия категории
            $data['alias'] = Model::str2url($data['name']);
            $model = new Model();
            if($model->updateTable('categories', $data, 'id', $id)){
                $success = '<li class="success">Изменения успешно внесены</li>';
                $_SESSION['success'] = "<ul>$success</ul>";
                redirect();
            }
        }
        $id = $this->getRequestID();
        $model = new Category();
        $category = $model->findOne($id, 'id', 'categories')[0];
        App::$app->setProperty('parent_id', $category['parent_id']);
        View::setMeta("Редактирование категории {$category['name']}", '', '');

        $this->set(compact('category'));
    }

}