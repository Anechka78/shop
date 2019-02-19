<?php
/**
 * Created by PhpStorm.
 * User: Anna
 * Date: 01.11.2018
 * Time: 18:12
 */

namespace im\core\base;
use im\core\Db;
use Valitron\Validator;

abstract class Model
{
    protected $pdo;
    protected $table;
    protected $pk = 'id';
    public $attributes = [];
    public $errors = [];
    public $rules = [];

    public function __construct(){
        $this->pdo = Db::instance();
    }

    /**
     * @param $data
     * Метод для автозагрузки данных
     * Сравнивает, есть ли в нужном классе Модели массив атрибутов $attributes и в том случае, если в массиве $data
     * содержатся эти аттрибуты, он их загружает, а если нет - не использует.
     */
    public function load($data){
        foreach($this->attributes as $name=>$value){
            if(isset($data[$name])){
                $this->attributes[$name] = $data[$name];
            }
        }
    }

    /**
     * Метод для вставки данных в таблицу БД
     * @param string $table - таблица, в которую будут добавлены данные
     * @return mixed
     */
    public function save($table = ''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу

        $sql = 'INSERT INTO '. $table. '(`';
        $sql .= implode("`, `", array_keys($this->attributes)).'`)';
        $sql .= ' VALUES (:';
        $sql .= implode(", :", array_keys($this->attributes)).')';
        //var_dump($sql);
        return $this->pdo->execute($sql, $this->attributes);
    }

    /**
     * @param $data - массив данных для проверки
     * Валидация входящих данных
     * @return mixed
     */
    public function validate($data){
        Validator::langDir(WWW.'/valitron/lang');
        Validator::lang('ru');
        $v = new Validator($data);
        $v->rules($this->rules);//список правил определяем в модели User
        if($v->validate()){
            return true;
        }
        $this->errors = $v->errors();//если есть ошибки - записываем их в св-во ошибок
        return false;
    }

    /**
     * Метод выводит ошибки при валидации данных
     */
    public function getErrors(){
        $errors = '';
        foreach($this->errors as $error){
            foreach($error as $item){
                $errors .= "$item";
            }
        }
        $errors .= ' ';
        $_SESSION['error'] = $errors;
    }

    /**
     * Надстройка над методом execute для запросов, в которых нужно в виде ответа получить true/false
     * @param $sql
     * @return bool
     */
    public function query($sql){
        return $this->pdo->execute($sql);
    }

    /**
     * Метод возвращает количество записей
     * @param string $table
     * @return mixed
     */
    public function selectCount($table = ''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM $table");
        $nRows = $stmt[0]['COUNT(*)'];
        return $nRows;
    }

    /**
     * Метод возвращает количество записей, удовлетворяющих определенным условиям
     * @param $table
     * @param $field
     * @param $id
     * @return mixed
     */
    public function selectCountFromTable($table, $field, $id){
        //debug($id); die();
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM $table WHERE $field = '{$id}'");
        $nRows = $stmt[0]['COUNT(*)'];
        return $nRows;
    }

    public function findAll(){
        $sql = "SELECT * FROM {$this->table}";
        return $this->pdo->query($sql);
    }

    public function findOne($id, $field = '', $table=''){ //$field - параметр, указывающий по какому полю мы хотим выбирать данные
        $table = $table ?: $this->table;
        $field = $field ?: $this->pk; //если передано значение, по которому отбирать и оно не равно id - берем из модели
        $sql = "SELECT * FROM $table WHERE $field = ? LIMIT 1";
        return $this->pdo->query($sql, [$id]); //$id - в большинстве случаев - числовое значение, по которому отбираем
    }

    public function findBySql($sql, $params = []){
        return $this->pdo->query($sql, $params);
    }

    public function findLike($str, $field, $table = ''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        $sql = "SELECT * FROM $table WHERE $field LIKE ? LIMIT 10";
        return $this->pdo->query($sql, ['%'. $str .'%']);
    }

    public function findEqual($str, $field, $table = ''){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        $sql = "SELECT * FROM $table WHERE $field = ?";
        return $this->pdo->query($sql, [$str]);
    }

    public function findAllLimit($table = '', $start, $perpage){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        $sql = "SELECT * FROM $table LIMIT :start, :perpage";
        return $this->pdo->query($sql, array('start' => $start, 'perpage' => $perpage));
    }

    /** Удаление строки из таблицы
     * @param string $table - название таблицы в БД из которой удаляем
     * @param string $second_id - если есть - второй параметр, по которому удаляем
     * @param string $second_field - если есть - второе поле в таблице, по которому удаляем данные
     * @param $id - что удаляем
     * @param $id_field - поле в БД, по которому удаляем
     * @return mixed
     */
    public function deleteItemFromTable($table = '', $second_id='', $second_field='', $id, $id_field){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу
        if(!empty($second_id) && !empty($second_field)){
            $sql = "DELETE FROM $table WHERE $second_field =  ? AND $id_field = ?";
            $result = $this->findBySql($sql, [$second_id, $id]);
        }else{
            $sql = "DELETE FROM $table WHERE $id_field = ?";
            $result = $this->findBySql($sql, [$id]);
        }
        return $result;
    }

    /*Функция, которая принимает на вход массивы и определяет по логике, что главней и находит данные со сверкой с БД*/
    /* Используется для цены и наличия кол-ва товара*/
    public function getValueFromArrays($product, $value = ''){
        // debug($product); debug($value); die();
        if(isset($product['prDepArr'])){
            if($product['prDepArr'][$value] != 0){
                $myvalue = $product['prDepArr'][$value];
                $sql = "SELECT $value FROM `properties_dependences` WHERE `id` = ? AND `product_id` = ?";
                $item = $this->findBySql($sql, [$product['prDepArr']['id'], $product['id']])[0];

                if($myvalue != $item[$value]){
                    $myvalue = $item[$value];
                }
                return $myvalue;
            }
        }else if(isset($product['prValArr'])){
            foreach($product['prValArr'] as $key=>$val){
                if($val[$value] != 0){

                    $myvalue = $val[$value];
                    $sql = "SELECT $value FROM `product_properties_values` WHERE `id` = ?";
                    $item = $this->findBySql($sql, [$val['id']])[0];

                    if($myvalue != $item[$value]){
                        $myvalue = $item[$value];
                    }
                    return $myvalue;
                }
            }
        }
        $myvalue = $product[$value];
        $sql = "SELECT $value FROM `products` WHERE `id` = ?";
        $item = $this->findBySql($sql, [$product['id']])[0];

        if($myvalue != $item[$value]){
            $myvalue = $item[$value];
        }
        return $myvalue;
    }

    /**
     * Метод для вставки данных в таблицу БД
     * @param string $table - таблица, в которую будут добавлены данные
     * @return mixed
     */
    public function insertAndReturnId($table = '', $data){
        $table = $table ?: $this->table; //если передана таблица - берем ее, нет - берем указанную в модели таблицу

        $sql = 'INSERT INTO '. $table. '(`';
        $sql .= implode("`, `", array_keys($data)).'`)';
        $sql .= ' VALUES (:';
        $sql .= implode(", :", array_keys($data)).')';
        return $this->pdo->lastId($sql, $data);
    }

    /**
     * @param $table - таблица, в которой проводим изменения
     * @param array $data массив поле-значение, которые подставляются в таблицу
     * @param string $field столбец, по которому понимаем, в какую строку вносить изменения, например id столбца
     * @param $str значение в столбце, которое говорит о том, где производить изменения
     */
    public function updateTable($table, array$data, $field, $str){
        //debug($str);
        $sql = 'UPDATE '. $table . ' SET ';
        foreach($data as $k=>$v){
            $sql .= $k . ' = :' . $k.', ';
        }
        $sql = substr($sql, 0, -2);
        $sql .= ' WHERE `'.$field.'` ='.$str;

        return $this->pdo->execute($sql, $data);
    }

}