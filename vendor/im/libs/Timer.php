<?php

namespace im\libs;


/**
 * ����� ��� ��������� ������� ���������� ������� ��� ��������
 */
class Timer
{
    /**
     * @var float ����� ������ ���������� �������
     */
    private static $start = .0;

    /**
     * ������ ����������
     */
    static function start()
    {
        self::$start = microtime(true);
    }

    /**
     * ������� ����� ������� ������ ������� � ������ self::$start
     * @return float
     */
    static function finish()
    {
        return microtime(true) - self::$start;
    }
}