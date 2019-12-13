<?php

namespace app\models\admin;


use im\core\base\Model;

class Product extends Model{

    public $attributes = [
        'name'=> '',
        'title'=> '',
        'category_id'=> '',
        'vendor'=> '',
        'description'=> '',
        'meta_desc'=> '',
        'price'=> '',
        'old_price'=> '',
        'count'=> '',
        'weight'=> '',
        'hit'=> '',
        'status'=> '',
        'alias'=> '',
    ];

    public $rules = [
        'required' => [
            ['title'],
            ['category_id'],
            ['price']
        ],
        'integer' => [
            ['category_id'],
            ['vendor'],
            ['count'],
        ],
        'numeric' => [
            ['price'],
            ['old_price'],
            ['weight'],
        ],
    ];

    public function getAllProducts($start, $perpage, $val, $sort){
        $sql = "SELECT pr.id as id, pr.name as name, pr.alias as alias, pr.price, pr.vendor, pr.status, pr.hit, cat.name as category, v.name as vendor_name  FROM `products` AS `pr`
                    JOIN `categories` AS `cat` ON pr.category_id = cat.id
                    JOIN `vendor` AS `v` ON pr.vendor = v.id ORDER BY {$val} {$sort}
                    LIMIT :start, :perpage";
        $products = $this->findBySql($sql, array('start' => $start, 'perpage' => $perpage));
        return $products;
    }

    public function getAllVendors(){
        $sql = "SELECT `id`, `name` FROM vendor";
        $vendors = $this->findBySql($sql, []);
        return $vendors;
    }

    public function getAllMods(){
        $sql = "SELECT `id`, `name` FROM properties";
        $properties = $this->findBySql($sql, []);
        return $properties;
    }

    public function checkProductParams($param, $elem, $message){
        if($param == '0' || $param == '') {
            if (isset($_SESSION['pd'])) {
                foreach ($_SESSION['pd'] as $k => $v) {
                    if ($v[$elem] == '0' || $v[$elem] == '') {
                        $_SESSION['error'] = $message;
                        redirect();
                    }
                }
            } elseif(!isset($_SESSION['pd']) && isset($_SESSION['pv'])){
                foreach ($_SESSION['pv'] as $k => $v) {
                    if ($v[$elem] == '0' || $v[$elem] == '') {
                        $_SESSION['error'] = $message;
                        redirect();
                    }
                }
            }elseif(!isset($_SESSION['pd']) && !isset($_SESSION['pv'])){
                $_SESSION['error'] = $message;
                redirect();
            }
        }
    }

    public function addProductDependenciesInDb($id, $category_id){
        $flag_count = false;
        if(!empty($_SESSION['pd'])) {
            $sql_part = '';
            $filters = [];
            foreach ($_SESSION['pd'] as $k => $v) {
                if($v['count'] > 0){
                    $flag_count = true;
                }
                $sql_part .= "({$v['parent_property_name']}, {$v['parent_property_names']},
                            {$v['child_property_name']}, {$v['child_property_names']},
                            {$v['count']}, {$v['price']}, {$v['old_price']}, {$v['weight']}, $id, $category_id),";

                if(isset($_SESSION['pv'])){
                    foreach($_SESSION['pv'] as $i=>$pv){
                        if($pv['property_value_value'] == $v['parent_property_names'] || $pv['property_value_value'] == $v['child_property_names']){
                            unset($_SESSION['pv'][$i]);
                        }
                    }
                }
                if(!in_array($v['parent_property_names'], $filters)){
                    $filters[$k][] = $v['parent_property_names'];
                }
                if(!in_array($v['child_property_names'], $filters)){
                    $filters[$k][] = $v['child_property_names'];
                }
                if(isset($_SESSION['pv'])){
                    foreach($_SESSION['pv'] as $ind=>$val){
                        if(!in_array($val['property_value_value'], $filters)){
                            $filters[$k][] = $val['property_value_value'];
                        }
                    }
                }
            }

            foreach($filters as $f_key=>$f_val){
                $filter_sql = '';
                $filter_sql .= "("."`product_id`, "."`".rtrim(implode('`,`', $f_val), ',')."`".")";
                $filter_table = 'filter_'.$category_id;
                $in = "'$id', ".join(',', array_fill(0, count($f_val), "'1'"));
                foreach($f_val as $filter_id=>$filter_val){
                    $sql_for_table = "SELECT count(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS
                                 WHERE TABLE_SCHEMA = 'nuha' AND TABLE_NAME = '".$filter_table."' AND COLUMN_NAME=?";
                    $res = $this->findBySql($sql_for_table, [$filter_val])[0];

                    if($res['count(COLUMN_NAME)'] !== 1){
                        $sql_in_table = "ALTER TABLE `".$filter_table."` ADD `{$filter_val}` enum('0', '1') NOT NULL DEFAULT '0'";
                        $this->findBySql($sql_in_table, []);
                    }
                }
                $sql_prop = "INSERT INTO `".$filter_table."` {$filter_sql} VALUES ({$in})";
                $this->findBySql($sql_prop, []);
            }

        $sql_part = rtrim($sql_part, ',');

        $sql = "INSERT INTO `properties_dependences`
              (`parent_property_name`, `parent_property_names`, `child_property_name`, `child_property_names`,
               `count`, `price`, `old_price`, `weight`, `product_id`, `category_id`) VALUES {$sql_part}";
        $this->findBySql($sql, []);
        unset($_SESSION['pd']);
        }
        //если существуют взаимозависимые характеристики и простые, при этом у простых есть кол-во, цена, вес и старая цена -
        //удаляем эти значения из сессии простых характеристик, чтобы не было конфликта

        if($flag_count === true){
            if(isset($_SESSION['pv'])){
                foreach($_SESSION['pv'] as $key=>$val){
                    $_SESSION['pv'][$key]['count'] = '0';
                    $_SESSION['pv'][$key]['price'] = '0';
                    $_SESSION['pv'][$key]['old_price'] = '0';
                    $_SESSION['pv'][$key]['weight'] = '0';
                }
            }
            //var_dump($_SESSION['pv']); die();
        }
    }

    public function addProductValuesInDb($id, $category_id){
        if(!empty($_SESSION['pv'])){
            $filter_table = 'filter_'.$category_id;
            $result = $this->selectCountFromTable($filter_table, 'product_id', $id);
            $filters = [];
            if($result == 0){
                foreach($_SESSION['pv'] as $k => $v) {
                    $filters[] = $v['property_value_value'];
                }
                $filter_sql = '';
                $filter_sql .= "("."`product_id`, "."`".rtrim(implode('`,`', $filters), ',')."`".")";
                $filter_table = 'filter_'.$category_id;
                $in = "'$id', ".join(',', array_fill(0, count($filters), "'1'"));
                foreach($filters as $filter_id=>$filter_val){
                    $sql_for_table = "SELECT count(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS
                                 WHERE TABLE_SCHEMA = 'nuha' AND TABLE_NAME = '".$filter_table."' AND COLUMN_NAME=?";
                    $res = $this->findBySql($sql_for_table, [$filter_val])[0];

                    if($res['count(COLUMN_NAME)'] !== 1){
                        $sql_in_table = "ALTER TABLE `".$filter_table."` ADD `{$filter_val}` enum('0', '1') NOT NULL DEFAULT '0'";
                        $this->findBySql($sql_in_table, []);
                    }
                }
                $sql_prop = "INSERT INTO `".$filter_table."` {$filter_sql} VALUES ({$in})";
                //var_dump($sql_prop); die();
                $this->findBySql($sql_prop, []);
            }

            $sql_part = '';
            foreach ($_SESSION['pv'] as $k => $v) {
                if(empty($v['count'])){
                    $v['count'] = '0';
                }
                if(empty($v['price'])){
                    $v['price'] = '0';
                }
                if(empty($v['old_price'])){
                    $v['old_price'] = '0';
                }
                if(empty($v['weight'])){
                    $v['weight'] = '0';
                }

                $sql_part .= "({$v['property_value_value']},
                            {$v['count']}, {$v['price']}, {$v['old_price']}, {$v['weight']}, $id, $category_id),";
            }
            $sql_part = rtrim($sql_part, ',');
           // var_dump($sql_part); die();

            $sql = "INSERT INTO `product_properties_values`
              (`prop_val_id`, `count`, `price`, `old_price`, `weight`, `product_id`, `category_id`) VALUES {$sql_part}";
            $this->findBySql($sql, []);
            unset($_SESSION['pv']);
        }
    }

    public function getModValues($id)
    {
        $sql = "SELECT `id`, `name` FROM `properties_values` WHERE `property_id` = ?";
        $mod_values = $this->findBySql($sql, [$id]);
        return $mod_values;
    }

    public function editRelatedProducts($id, $related){
        $sql = "SELECT `related_id` FROM `related_products` WHERE `product_id` = ?";
        $related_products = $this->findBySql($sql, [$id]);

        // если менеджер убрал связанные товары - удаляем их
        if(empty($related) && !empty($related_products)){
            $sql = "DELETE FROM `related_products` WHERE `product_id` = ?";
            $this->findBySql($sql, [$id]);
            return;
        }
        // если добавляются связанные товары
        if(empty($related_products) && !empty($related)){
            $sql_part = '';
            foreach($related as $v){
                $v = (int)$v;
                $sql_part .= "($id, $v),";
            }
            $sql_part = rtrim($sql_part, ',');
            $sql = "INSERT INTO `related_products` (`product_id`, `related_id`) VALUES {$sql_part}";
            $this->findBySql($sql, []);
            return;
        }
        //сравниваем массивы на наличие изменений в БД и заполненных полях
        if(!empty($related)){
            $result = array_diff($related_products, $related);
            if(!empty($result) || count($related_products) != count($related)){
                $sql = "DELETE FROM `related_products` WHERE `product_id` = ?";
                $this->findBySql($sql, [$id]);
                $sql_part = '';
                foreach($related as $v){
                    $v = (int)$v;
                    $sql_part .= "($id, $v),";
                }
                $sql_part = rtrim($sql_part, ',');
                $sql = "INSERT INTO `related_products` (`product_id`, `related_id`) VALUES {$sql_part}";
                $this->findBySql($sql, []);

            }
        }
    }

    public function saveGallery($id){
        if(!empty($_SESSION['multi'])){
            $sql_part = '';
            foreach($_SESSION['multi'] as $v){
                $sql_part .= "('$v', $id),";
            }
            $sql_part = rtrim($sql_part, ',');
            $sql = "INSERT INTO `productimages` (`image`, `product_id`) VALUES {$sql_part}";
            $this->findBySql($sql, []);
           // \R::exec("INSERT INTO gallery (img, product_id) VALUES $sql_part");
            unset($_SESSION['multi']);
        }
    }

    public function uploadImg($name, $wpmax, $hpmax, $wmax, $hmax){
        $uploaddir = WWW . '/images/products/';
        //$ext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES[$name]['name'])); // расширение картинки
        $ext = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
        $filename = pathinfo($_FILES[$name]['name'], PATHINFO_FILENAME);
        //debug($_FILES[$name]); die;
        $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых расширений
        if($_FILES[$name]['size'] > 1048576){
            $res = array("error" => "Ошибка! Максимальный вес файла - 1 Мб!");
            exit(json_encode($res));
        }
        if($_FILES[$name]['error']){
            $res = array("error" => "Ошибка! Возможно, файл слишком большой.");
            exit(json_encode($res));
        }
        if(!in_array($_FILES[$name]['type'], $types)){
            $res = array("error" => "Допустимые расширения - .gif, .jpg, .png");
            exit(json_encode($res));
        }
        $new_name = $filename . '_' . time() . '.' . $ext;
        //$new_name = md5(time()).".$ext";
        $uploadfile = $uploaddir.$new_name;
        if(@move_uploaded_file($_FILES[$name]['tmp_name'], $uploadfile)){
            if(empty($_SESSION['single'])){
                $_SESSION['single'] = $new_name;
                $newdir = WWW . '/images/products/prev/'.$new_name;
                self::resize($uploadfile, $newdir, $wpmax, $hpmax, $ext);
            }
            $_SESSION['multi'][] = $new_name;

            self::resize($uploadfile, $uploadfile, $wmax, $hmax, $ext);
            $res = array("file" => $new_name);
            //debug($res); die;
            exit(json_encode($res));
        }
    }

    /**
     * @param string $target путь к оригинальному файлу
     * @param string $dest путь сохранения обработанного файла
     * @param string $wmax максимальная ширина
     * @param string $hmax максимальная высота
     * @param string $ext расширение файла
     */
    public static function resize($target, $dest, $wmax, $hmax, $ext){
        list($w_orig, $h_orig) = getimagesize($target);
        $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная

        if(($wmax / $hmax) > $ratio){
            $wmax = $hmax * $ratio;
        }else{
            $hmax = $wmax / $ratio;
        }

        $img = "";
        // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
        switch($ext){
            case("gif"):
                $img = imagecreatefromgif($target);
                break;
            case("png"):
                $img = imagecreatefrompng($target);
                break;
            default:
                $img = imagecreatefromjpeg($target);
        }
        $newImg = imagecreatetruecolor($wmax, $hmax); // создаем оболочку для новой картинки

        if($ext == "png"){
            imagesavealpha($newImg, true); // сохранение альфа канала
            $transPng = imagecolorallocatealpha($newImg,0,0,0,127); // добавляем прозрачность
            imagefill($newImg, 0, 0, $transPng); // заливка
        }

        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и ресайзим изображение
        switch($ext){
            case("gif"):
                imagegif($newImg, $dest);
                break;
            case("png"):
                imagepng($newImg, $dest);
                break;
            default:
                imagejpeg($newImg, $dest);
        }
        imagedestroy($newImg);
    }


    //Вносим данные о продукте в таблицу jsonproduct
    public function jsonproduct($id, $alias, $product){
        $sql = "INSERT  INTO `jsonproduct` (`product_id`, `product_alias`,`product_json`) VALUES (
                 ?, ?, ?)";
        $jsonprod = $this->findBySql($sql, [$id, $alias, $product]);
    }
    //Получаем данные о продукте из таблицы jsonproduct
    public function getProductByJson($id = 'product_alias', $alias){
        $sql = "SELECT pr.*, cat.name as cat_name, cat.alias as cat_alias, vn.name as vn_name FROM `jsonproduct` AS pr
                LEFT JOIN `categories` AS `cat` ON cat.id = CAST(JSON_EXTRACT(`product_json`, '$.category_id') AS SIGNED)
                LEFT JOIN `vendor` AS `vn` ON CAST(product_json->'$.vendor' AS SIGNED) = vn.id
                WHERE {$id} = ?";
        $product = $this->findBySql($sql, [$alias])[0];
//var_dump($product); die();
        return $product;
    }

    /**
     * Вносим данные о продукте в таблицу products_search для поиска по ним
     * @param $id - идентификатор продукта из таблицы products
     * @param $name - название продукта
     * @param $description - описание продукта
     */
    public function setProductToSearch($id, $name, $description){
        $sql = "INSERT  INTO `products_search` (`product_id`, `product_name`,`product_description`) VALUES (
                 ?, ?, ?)";
        $result = $this->findBySql($sql, [$id, $name, $description]);
    }



}