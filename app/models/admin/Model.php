<?php

namespace app\models\admin;


class Model extends \im\core\base\Model
{
    /**
     * Создание алиаса
     * @param $table таблица
     * @param $field поле, для которого создаем 'alias'
     * @param $str строка, из которой создаем алиас - значение записи (трусы мужские в цветочек)
     * @param $id id той записи, для которой нужно сделать транслитерацию
     * @return mixed|string
     */
    public function createAlias($table, $field, $str, $id=''){
        $str = self::str2url($str);
        $res = $this->findOne($str, $field, $table);
        if($res){
            $str = "{$str}-{$id}";
            $res = $this->selectCountFromTable('categories', $field, $str);
            if($res){
                $str = $this->createAlias($table, $field, $str, $id);
            }
        }
        return $str;
    }

    public static function str2url($alias) {
        $alias = mb_strtolower($alias, 'utf-8');
        $alias = preg_replace('#&\w{2,80};#', ' ', $alias);
        $alias = str_replace(
            array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'),
            array('a','b','v','g','d','e','yo','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya'),
            $alias
        );
        $alias = preg_replace('#[^a-z0-9]#', '-', $alias);
        $alias = trim(preg_replace('#-+#', '-', $alias), '-');
        return $alias;
    }
}