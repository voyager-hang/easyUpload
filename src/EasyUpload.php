<?php

namespace EasyUpload;

use EasyUpload\config\Config;
use EasyUpload\library\OssUpload;
use EasyUpload\library\QiNiuUpload;
use EasyUpload\library\SysUpload;
use EasyUpload\struct\ConfigStruct;
use Exception;

/**
 * Class EasyUpload 工厂类
 * @package EasyUpload
 */
class EasyUpload
{
    private static SysUpload $sysUpload;
    private static QiNiuUpload $qnUpload;
    private static OssUpload $ossUpload;
    private static string $uploadServer;
    private static $config;

    public static function setConfig(array $config)
    {
        self::$config = $config;
    }

    /**
     * @desc:获取上传实单例
     * @param false $newObj
     * @return OssUpload|QiNiuUpload|SysUpload
     * @date: 2021/6/21
     * @time: 6:18 下午
     */
    public static function Instance(bool $newObj = false)
    {
        $config = self::getConfig();
        if (!empty(self::$uploadServer)) {
            $config->setUploadServer(self::$uploadServer);
        }
        //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
        switch ($config->getUploadServer()) {
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

    /**设置上传位置
     * @param string $uploadServer
     */
    public static function setUploadServer(string $uploadServer)
    {
        self::$uploadServer = $uploadServer;
    }

    /**
     * @desc: 获取配置
     * @return ConfigStruct
     */
    public static function getConfig(): ConfigStruct
    {
        $config = self::$config;
        if (empty($config) || !is_array($config)) {
            $tpCof = '\think\facade\Config';
            $useMyConfigFile = true;
            if (class_exists($tpCof)) {
                try {
                    $cof = $tpCof::pull('EasyUpload');
                } catch (\Exception $e) {
                    $cof = $tpCof::get('EasyUpload');
                }
                if (!empty($cof)) {
                    Config::setCof($cof);
                    $config = Config::def();
                    $useMyConfigFile = false;
                }
            }
            // 判断是否使用自己的配置文件
            if ($useMyConfigFile) {
                $dir = str_ireplace('\\', '/', __DIR__);
                $webPathLoc = stripos($dir, 'vendor/yuanhang/easy-upload');
                $webPath = substr($dir, 0, $webPathLoc);
                if (file_exists($webPath . 'EasyUpload/Config.php')) {
                    $config = require $webPath . 'EasyUpload/Config.php';
                    if (!is_array($config)) {
                        throw new Exception('The configuration file is not an array, the file is in "' . $webPath . 'EasyUpload/Config.php"');
                    }
                } else if (file_exists($webPath . 'config/EasyUpload.php')) {
                    $config = require $webPath . 'config/EasyUpload.php';
                    if (!is_array($config)) {
                        throw new Exception('The configuration file is not an array, the file is in "' . $webPath . 'config/EasyUpload.php"');
                    }
                } else {
                    // 默认配置
                    $defConf = Config::def();
                    $config = $defConf;
                }
            }
        }
        if (is_array($config)) {
            Config::setCof($config);
            $config = Config::def();
        }
        return $config;
    }

    /**
     * @desc: 获取配置文件目录
     * @return string[]
     */
    public static function getConfigPath(): array
    {
        $dir = str_ireplace('\\', '/', __DIR__);
        $webPathLoc = stripos($dir, 'vendor/yuanhang/easy-upload');
        $webPath = substr($dir, 0, $webPathLoc);
        return [
            $webPath . 'EasyUpload/Config.php',
            $webPath . 'config/EasyUpload.php'
        ];
    }

    /**
     * @desc: 实例化系统上传
     * @param $config
     * @param $newObj
     * @return SysUpload
     * @date: 2021/6/21
     * @time: 6:15 下午
     */
    private static function SysUpload($config, $newObj): SysUpload
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
     * @date: 2021/6/21
     * @time: 6:15 下午
     */
    private static function OssUpload($config, $newObj): OssUpload
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
     * @date: 2021/6/21
     * @time: 6:16 下午
     */
    private static function QnUpload($config, $newObj): QiNiuUpload
    {
        if ($newObj || empty(self::$qnUpload)) {
            self::$qnUpload = new QiNiuUpload($config);
        }
        return self::$qnUpload;
    }
}