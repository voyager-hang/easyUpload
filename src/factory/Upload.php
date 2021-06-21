<?php

namespace EasyUpload\factory;
class Upload
{
    private static $aliDaYuObj;

    public static function createOperation($type = '')
    {
        if (empty($type)) {
            $type = Config::get('sms.type');
        }
        $class = '';
        switch ($type) {
            case "alidayu":
                $class = self::alidayu();
                break;
            default:
        }
        return $class;
    }

    private static function alidayu()
    {
        if (empty(self::$aliDaYuObj)) {
            self::$aliDaYuObj = new AliDaYu();
        }
        return self::$aliDaYuObj;
    }
}