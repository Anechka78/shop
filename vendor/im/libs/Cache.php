<?php

namespace im\libs;


class Cache
{
    public function __construct(){

    }

    /**
     * @param $key - название тех данных, которые мы кладем в кеш(posts, menu etc)
     * @param $data - сами данные, которые мы кешируем
     * @param int $seconds
     */
    public function set($key, $data, $seconds = 3600){
        //высчитаем конечную дату для кешированных данных, если дата меньше текущей - кеш уже не актуален
        $content['data'] = $data;
        $content['endtime'] = time() + $seconds;
        if(file_put_contents(CACHE . '/' . md5($key) . '.txt', serialize($content))){
            return true;
        }
        return false;
    }

    public function get($key){
        $file = CACHE . '/' . md5($key) . '.txt';
        if(file_exists($file)){
            $content = unserialize(file_get_contents($file));
            /**
             * если текущее время меньше срока окончания кеша - возвращаем кеш
             */
            if(time() <= $content['endtime']){
                return $content['data'];
            }
            unlink($file); //удаляем запрошенный файл, если срок кеша вышел
        }
        return false;
    }

    public function delete($key){
        $file = CACHE . '/' . md5($key) . '.txt';
        if(file_exists($file)){
            unlink($file); //удаляем запрошенный файл, если срок кеша вышел
        }
    }

}