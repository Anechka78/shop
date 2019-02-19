<?php

namespace im\core;
/**
 * ����� ��� �������� ������ ���� ��������
 * Trait TSingleton
 * @package vendor\core
 */

trait TSingleton{

    protected static $instance;

    public static function instance(){
        if(self::$instance === null){ //���� ��-�� �����
            self::$instance = new self; //������� � ���� ������ ������� ������
        }
        return self::$instance; //������ ���� ������
    }

}