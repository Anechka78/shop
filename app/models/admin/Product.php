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

    public function addProductDependenciesInDb($id){
        $flag_count = false;
        if(!empty($_SESSION['pd'])) {
            $sql_part = '';
            foreach ($_SESSION['pd'] as $k => $v) {
                if($v['count'] > 0){
                    $flag_count = true;
                }
                $sql_part .= "({$v['parent_property_name']}, {$v['parent_property_names']},
                            {$v['child_property_name']}, {$v['child_property_names']},
                            {$v['count']}, {$v['price']}, {$v['old_price']}, {$v['weight']}, $id),";

                if(isset($_SESSION['pv'])){
                    foreach($_SESSION['pv'] as $i=>$pv){
                        if($pv['property_value_value'] == $v['parent_property_names'] || $pv['property_value_value'] == $v['child_property_names']){
                            unset($_SESSION['pv'][$i]);
                        }
                    }
                }
            }
        $sql_part = rtrim($sql_part, ',');
            //var_dump($flag_count);

        $sql = "INSERT INTO `properties_dependences`
              (`parent_property_name`, `parent_property_names`, `child_property_name`, `child_property_names`,
               `count`, `price`, `old_price`, `weight`, `product_id`) VALUES {$sql_part}";
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

    public function addProductValuesInDb($id){
        if(!empty($_SESSION['pv'])){
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
                            {$v['count']}, {$v['price']}, {$v['old_price']}, {$v['weight']}, $id),";
            }
            $sql_part = rtrim($sql_part, ',');
           // var_dump($sql_part); die();

            $sql = "INSERT INTO `product_properties_values`
              (`prop_val_id`, `count`, `price`, `old_price`, `weight`, `product_id`) VALUES {$sql_part}";
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
        //debug($data); die();
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
            //debug($sql_part); die();
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


}