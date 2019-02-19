<?php

namespace im\widgets\menu;


use im\core\App;
use im\libs\Cache;

class Menu{

    protected $data;
    protected $tree;
    protected $menuHtml;
    protected $tpl;
    protected $container = 'ul';
    protected $class = '';
    protected $table = 'category';
    protected $cache = 3600;
    protected $cacheKey = 'site_menu';
    protected $attrs = [];
    protected $prepend = '';

    public function __construct($options = []){
        $this->tpl = __DIR__ . '/menu_tpl/menu.php';
        $this->getOptions($options);
        $this->run();
    }

    protected function getOptions($options){
        foreach($options as $k => $v){
            if(property_exists($this, $k)){
                $this->$k = $v;
            }
        }
    }

    protected function run(){
        $cache = new Cache();
        $this->menuHtml = $cache->get($this->cacheKey);
       // var_dump($this->menuHtml);
        if(!$this->menuHtml){
            $this->data = App::$app->getProperty('cats');
            //var_dump($this->data);
            if(!$this->data){
                $model = new MenuModel();
                //$this->data = $model->findAll();
                $arr = $model->findAll();
                $arrid = [];
                foreach($arr as $k=> $v){
                    $arrid[]=$v['id'];
                }
                $this->data = array_combine($arrid, $arr);
            }

            $this->tree = $this->getTree();
            $this->menuHtml = $this->getMenuHtml($this->tree);
            if($this->cache){
                $cache->set($this->cacheKey, $this->menuHtml, $this->cache);
            }
        }
        $this->output();
    }

    protected function output(){
        $attrs = '';
        if(!empty($this->attrs)){
            foreach($this->attrs as $k => $v){
                $attrs .= " $k='$v' ";
            }
        }
        echo "<{$this->container} class='{$this->class}' $attrs>";
        echo $this->prepend;
        echo $this->menuHtml;
        echo "</{$this->container}>";
    }

    protected function getTree(){
        $tree = [];
        $data = $this->data;

        foreach ($data as $id=>&$node) {
            if (!$node['parent_id']){
                $tree[$id] = &$node;
            }else{
                $data[$node['parent_id']]['children'][$id] = &$node;
            }
        }
       // debug($tree);
        return $tree;
    }

    protected function getMenuHtml($tree, $tab = ''){
        $str = '';
        foreach($tree as $id => $cat){
            $str .= $this->catToTemplate($cat, $tab, $id);
        }
        return $str;
    }

    protected function catToTemplate($category, $tab, $id){
        ob_start();
        require $this->tpl;
        return ob_get_clean();
    }

}