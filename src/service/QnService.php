<?php


namespace EasyUpload\service;


use EasyUpload\config\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Exception;

class QnService
{
    private $config;
    private static $bucket;
    private static $qnAuth;
    private static $instance;

    /**初始化七牛云 单例
     * QnService constructor.
     */
    public function __construct()
    {
        //获取配置项，并赋值给对象$config
        $config = Config::get('qi_niu_config');
        if (empty(self::$bucket)) {
            self::$bucket = $config['bucket'];
        }
        $this->config = $config;
        if (empty(self::$qnAuth)) {
            // 初始化Auth状态
            $auth = new Auth($config['access_key'], $config['secret_key']);
            self::$qnAuth = $auth;
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
     * @author: lyh
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
     * @param $path 本地文件
     * @param string $bucket
     * @throws \Exception
     * @author: lyh
     * @date: 2021/6/22
     * @time: 2:28 下午
     */
    public function uploadFile($object, $path, $bucket = '')
    {
        if (!empty($bucket)) {
            $this->setBucket($bucket);
        }
        //覆盖上传
        $expires = $this->config['expires'];
        //自定义返回值
        $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
        $policy = array(
            'returnBody' => $returnBody
        );
        // 上传到存储后保存的文件名
        $key = $object;
        // 覆盖的文件名
        $keyToOverwrite = $key;
        // 生成上传 Token
        $upToken = self::$qnAuth->uploadToken(self::$bucket, $keyToOverwrite, $expires, $policy, true);
        // 要上传文件的本地路径
        $filePath = $path;
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($upToken, $key, $filePath);
        if ($err !== null) {
            throw new Exception(json_encode($err));
        }
        $ret['url'] = $this->httpPath($key);
        return $ret;
    }

    /**移动文件
     * @return bool
     * @author: lyh
     * @Date：2019/11/28
     * @Time：15:39
     */
    public function moveFile($formPath, $toPath)
    {
        $formPath = $this->pathHandle($formPath);
        $toPath = $this->pathHandle($toPath);
        $key = $formPath;
        $auth = self::$qnAuth;
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        $srcBucket = self::$bucket;
        $destBucket = self::$bucket;
        $err = $bucketManager->move($srcBucket, $key, $destBucket, $toPath, true);
        if (!empty($err[0])) {
            throw new Exception(json_encode($err));
        }
        return true;
    }

    /**删除文件
     * @param $objects
     * @return bool
     * @author: lyh
     * @Date：2019/11/28
     * @Time：16:38
     */
    public function delFile($objects)
    {
        if (!is_array($objects)) {
            $objects = [$objects];
        }
        foreach ($objects as $key => $val) {
            $objects[$key] = $this->pathHandle($val);
        }
        $auth = self::$qnAuth;
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        // 每次最多不能超过1000个
        if (count($objects) > 1000) {
            throw new Exception(Config::get('tips_message', 'del_file_max_num'));
        }
        $ops = $bucketManager->buildBatchDelete(self::$bucket, $objects);
        list($ret, $err) = $bucketManager->batch($ops);
        if ($err) {
            throw new Exception(json_encode($err));
        }
        return true;
    }

    /**
     * @name:
     * @desc:获取直接访问的地址
     * @param $path
     * @param string $suffix
     * @return array|false|mixed|string
     * @author: lyh
     * @date: 2021/6/22
     * @time: 2:59 下午
     */
    public function httpPath($path, $suffix = '', $emptyRes = '')
    {
        if (!is_array($path) && empty($path)) return $emptyRes;
        if (is_array($path)) {
            foreach ($path as $key => $url) {
                if (strpos($url, 'http') !== 0) {
                    $url = '/' . ltrim($url, '/');
                    $host = $this->config['http_host'];
                    $path[$key] = $host . $url . $suffix;
                } else {
                    $path[$key] .= $suffix;
                }
            }
        } else {
            $path = (string)$path;
            if (strpos($path, 'http') !== 0) {
                $path = '/' . ltrim($path, '/');
                $host = $this->config['http_host'];
                $path = $host . $path . $suffix;;
            }
        }
        return $path;
    }

    /**路径处理
     * @param $path
     * @return string
     * @author: lyh
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
        return trim($path, '/');
    }
}