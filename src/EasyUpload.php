<?php

namespace EasyUpload;

use EasyUpload\config\config;
use EasyUpload\library\ossUpload;
use EasyUpload\library\QiNiuUpload;
use EasyUpload\library\SysUpload;

/**
 * Class EasyUpload 工厂类
 * @package EasyUpload
 */
class EasyUpload
{
    private static $sysUpload;
    private static $qnUpload;
    private static $ossUpload;

    /**
     * @desc:获取上传实单例
     * @param false $newObj
     * @return ossUpload|QiNiuUpload|SysUpload
     * @author: lyh
     * @date: 2021/6/21
     * @time: 6:18 下午
     */
    public static function Instance($newObj = false)
    {
        $defConf = config::def();
        $config = $defConf;
        $tpCof = '\think\facade\Config';
        if (class_exists($tpCof)) {
            $cof = $tpCof::pull('EasyUpload');
            if (!empty($cof)) {
                $config = $cof;
            }
        }
        //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
        switch ($config['upload_server']) {
            case "oss":
                $class = self::OssUpload($config, $newObj);
                break;
            case "qn":
                $class = self::QnUpload($config, $newObj);
                break;
            default:
                $class = self::SysUpload($config, $newObj);
        }
        return $class;
    }

    /**
     * @desc: 实例化系统上传
     * @param $config
     * @param $newObj
     * @return SysUpload
     * @author: lyh
     * @date: 2021/6/21
     * @time: 6:15 下午
     */
    private static function SysUpload($config, $newObj)
    {
        if ($newObj || empty(self::$sysUpload)) {
            self::$sysUpload = new SysUpload($config);
        }
        return self::$sysUpload;
    }

    /**
     * @desc: 实例化oss上传
     * @param $config
     * @param $newObj
     * @return ossUpload
     * @author: lyh
     * @date: 2021/6/21
     * @time: 6:15 下午
     */
    private static function OssUpload($config, $newObj)
    {
        if ($newObj || empty(self::$ossUpload)) {
            self::$ossUpload = new OssUpload($config);
        }
        return self::$ossUpload;
    }

    /**
     * @desc: 实例化七牛上传
     * @param $config
     * @param $newObj
     * @return QiNiuUpload
     * @author: lyh
     * @date: 2021/6/21
     * @time: 6:16 下午
     */
    private static function QnUpload($config, $newObj)
    {
        if ($newObj || empty(self::$qnUpload)) {
            self::$qnUpload = new QiNiuUpload($config);
        }
        return self::$qnUpload;
    }
}