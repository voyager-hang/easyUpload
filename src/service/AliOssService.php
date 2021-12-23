<?php

namespace EasyUpload\service;


use EasyUpload\config\Config;
use OSS\Core\OssException;
use OSS\OssClient;

class AliOssService
{
    private $config;
    private static $bucket;
    private static $oss;
    private static $instance;

    /**初始化阿里云OSS 单例
     * AliOssService constructor.
     * @throws OssException
     */
    public function __construct()
    {
        //获取配置项，并赋值给对象$config
        $config = Config::get('oss_config');
        if (empty(self::$bucket)) {
            self::$bucket = $config['bucket'];
        }
        $this->config = $config;
        if (empty(self::$oss)) {
            //实例化OSS 保存到静态属性
            self::$oss = new OssClient($config['key_id'], $config['key_secret'], $config['endpoint']);
        }
    }

    //获取对象单例
    public static function getInstance()
    {
        //判断$instance是否是对象，不是则创建
        if (!self::$instance instanceof static) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**设置储存目录
     * @param $bucket
     * @Date：2019/11/27
     * @Time：11:30
     */
    public function setBucket($bucket = '')
    {
        if (empty($bucket)) {
            self::$bucket = $this->config['bucket'];
        } else {
            self::$bucket = $bucket;
        }
    }

    /**
     * @name:
     * @desc:上传指定的本地文件内容
     * @param $object
     * @param $path
     * @param string $bucket
     * @return mixed
     * @throws OssException
     * @date: 2021/6/22
     * @time: 2:28 下午
     */
    public function uploadFile($object, $path, $bucket = '')
    {
        $this->setBucket($bucket);
        $bucket = self::$bucket;
        try {
            $ossClient = self::$oss;
            $res = $ossClient->uploadFile($bucket, $object, $path);
            if (!empty($res['info']['url'])) {
                $def = $this->config['network_protocol'] . '://' . $bucket . '.' . $this->config['endpoint'];
                $host = empty($this->config['http_host']) ? $def : $this->config['http_host'];
                $res['info']['url'] = str_ireplace($def, $host, $res['info']['url']);
            }
            return $res['info'];
        } catch (OssException $e) {
            throw new OssException($e->getMessage());
        }
    }

    /**移动文件
     * @param bool $delOldFile
     * @return bool
     * @Date：2019/11/28
     * @Time：15:39
     */
    public function moveFile($formPath, $toPath, $delOldFile = true, $delOldDir = false)
    {
        $formPath = $this->pathHandle($formPath);
        $toPath = $this->pathHandle($toPath);
        try {
            self::$oss->copyObject(self::$bucket, $formPath, self::$bucket, $toPath);
        } catch (OssException $e) {
            return false;
        }
        if ($delOldDir) {
            $pathArr = explode('/', $formPath);
            $dir = '';
            foreach ($pathArr as $item) {
                if (strpos($item, '.') === false) {
                    if (empty($dir)) {
                        $dir .= $item;
                    } else {
                        $dir .= '/' . $item;
                    }
                }
            }
            if (!empty($dir)) {
                $this->delDir($dir);
            }
        } else if ($delOldFile) {
            $this->delFile($formPath);
        }
        return true;
    }

    /**复制文件
     * @name:
     * @desc:
     * @param $formPath
     * @param $toPath
     * @param string $from_bucket
     * @param string $to_bucket
     * @return bool|string
     * @date: 2021/6/22
     * @time: 11:02 上午
     */
    public function copyFile($formPath, $toPath, $from_bucket = '', $to_bucket = '')
    {
        $from_bucket = empty($from_bucket) ? self::$bucket : $from_bucket;
        $to_bucket = empty($to_bucket) ? self::$bucket : $to_bucket;
        $formPath = $this->pathHandle($formPath);
        $toPath = $this->pathHandle($toPath);
        try {
            self::$oss->copyObject($from_bucket, $formPath, $to_bucket, $toPath);
        } catch (OssException $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**删除文件
     * @param $objects
     * @return bool
     * @Date：2019/11/28
     * @Time：16:38
     */
    public function delFile($objects)
    {
        if (!is_array($objects)) {
            $objects = [$objects];
        }
        foreach ($objects as $key => $val) {
//            if (strpos($val, 'default') === false) {
            $objects[$key] = $this->pathHandle($val);
//            } else {
//                unset($objects[$key]);
//            }
        }
        try {
            if (!empty($objects)) {
                self::$oss->deleteObjects(self::$bucket, $objects);
            }
        } catch (OssException $e) {
            return false;
        }
        return true;
    }

    /**删除文件夹
     * @param $dir
     * @return bool
     * @Date：2019/11/28
     * @Time：16:38
     */
    public function delDir($dir)
    {
        $dir = $this->pathHandle($dir);
        if (!is_array($dir)) {
            $dir = [$dir];
        }
        try {
            foreach ($dir as $item) {
                $fileList = $this->getFileList($item, false);
                $this->delFile($fileList);
            }
        } catch (OssException $e) {
            return false;
        }
        return true;
    }

    /**路径处理
     * @param $path
     * @return string
     * @Date：2019/11/28
     * @Time：16:39
     */
    public function pathHandle($path)
    {
        $webUrl = $this->config['http_host'];
        $path = str_ireplace($webUrl, '', $path);

        if (strpos($path, 'https://') === 0) {
            $path = str_ireplace('https://', '', $path);
        } else if (strpos($path, 'http://') === 0) {
            $path = str_ireplace('http://', '', $path);
        }


        if (strpos($path, self::$bucket . '.') === 0) {
            $path = str_ireplace(self::$bucket . '.', '', $path);
        }
        $endpoint = $this->config['endpoint'];
        if (strpos($path, $endpoint . '/') === 0) {
            $path = str_ireplace($endpoint . '/', '', $path);
        }
        return trim($path, '/');
    }

    /**
     * @name:
     * @desc:获取直接访问的地址
     * @param $path
     * @param string $suffix
     * @return array|false|mixed|string
     * @date: 2021/6/22
     * @time: 2:59 下午
     */
    public function httpPath($path, $suffix = '', $emptyRes = '')
    {
        if (!is_array($path) && empty($path)) return $emptyRes;
        $config = $this->config;
        $bucket = empty(self::$bucket) ? $config['bucket'] : self::$bucket;
        if (is_array($path)) {
            foreach ($path as $key => $url) {
                if (strpos($url, 'http') !== 0) {
                    $url = '/' . ltrim($url, '/');
                    $host = empty($this->config['http_host']) ? $config['network_protocol'] . '://' . $bucket . '.' . $config['endpoint'] : $this->config['http_host'];
                    $path[$key] = $host . $url . $suffix;
                } else {
                    $path[$key] .= $suffix;
                }
            }
        } else {
            $path = (string)$path;
            if (strpos($path, 'http') !== 0) {
                $path = '/' . ltrim($path, '/');
                $host = empty($this->config['http_host']) ? $config['network_protocol'] . '://' . $bucket . '.' . $config['endpoint'] : $this->config['http_host'];
                $path = $host . $path . $suffix;;
            }
        }
        return $path;
    }
}