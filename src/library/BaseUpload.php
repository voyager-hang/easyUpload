<?php


namespace EasyUpload\library;


use EasyUpload\config\Config;
use EasyUpload\tool\Util;
use Exception;

class BaseUpload
{
    // 是否多文件上传
    protected $multiple;
    // 当前配置文件
    protected $config;
    // 当前上传的文件对象
    protected $fileObj;
    // 当前上传的文件对象数组
    protected $fileObjArr;
    //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
    protected $uploadServer;
    //命名方式  md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
    protected $giveName;
    //是否开启mimes验证
    protected $mimes;
    //临时文件夹
    protected $tempDir;
    //允许的图片文件上传目录
    protected $imgPath;
    //允许的图片文件MIME类型
    protected $imgMimes;
    //允许的图片文件后缀名
    protected $imgExt;
    //允许的图片文件大小
    protected $imgSize;
    //允许的其他文件上传目录
    protected $filePath;
    //允许的其他文件MIME类型
    protected $fileMimes;
    //允许的其他文件后缀名
    protected $fileExt;
    //允许的其他文件大小
    protected $fileSize;
    //文件类型 image|file
    protected $fileType;
    //路径过滤
    protected $filter;
    //自定义域名
    protected $httpHost;
    //oss配置
    protected $ossConfig;
    //七牛配置
    protected $qiNiuConfig;
    //提示信息
    protected $tipsMessage;

    public function __construct($config = [])
    {
        $defConf = Config::def();
        if (empty($config)) {
            $config = $defConf;
            $tpCof = '\think\facade\Config';
            if (class_exists($tpCof)) {
                try {
                    $cof = $tpCof::pull('EasyUpload');
                } catch (\Exception $e) {
                    $cof = $tpCof::get('EasyUpload');
                }
                if (!empty($cof)) {
                    $config = $cof;
                }
            }
        }
        $this->config = $config;
        //上传访问 oss(阿里云oss)，server(服务器 默认)，qn(七牛)
        $uploadServer = isset($config['upload_server']) ? $config['upload_server'] : $defConf['upload_server'];
        if (in_array($uploadServer, ['oss', 'server', 'qn'])) {
            $this->uploadServer = $uploadServer;
        } else {
            $this->uploadServer = 'server';
        }
        //是否开启mimes验证
        $this->mimes = isset($config['mimes']) ? $config['mimes'] : $defConf['mimes'];
        //临时文件夹
        $tempDir = isset($config['temp_dir']) ? $config['temp_dir'] : $defConf['temp_dir'];
        if (substr($tempDir, 0, strlen('.')) === '.') {
            $tempDir = ltrim($tempDir, '.');
        }
        $tempDir = ltrim(ltrim($tempDir, '\\'), '/');
        $this->tempDir = '.' . DIRECTORY_SEPARATOR . $tempDir;
        //允许的图片文件上传目录
        $imgPath = isset($config['img_path']) ? $config['img_path'] : $defConf['img_path'];
        if (substr($imgPath, 0, strlen('.')) === '.') {
            $imgPath = ltrim($imgPath, '.');
        }
        $imgPath = ltrim(ltrim($imgPath, '\\'), '/');
        $this->imgPath = '.' . DIRECTORY_SEPARATOR . $imgPath;
        //允许的图片文件MIME类型
        $this->imgMimes = isset($config['img_mimes']) ? $config['img_mimes'] : $defConf['img_mimes'];
        //允许的图片文件后缀名
        $this->imgExt = isset($config['img_ext']) ? $config['img_ext'] : $defConf['img_ext'];
        //允许的图片文件大小
        $this->imgSize = isset($config['img_size']) ? $config['img_size'] : $defConf['img_size'];
        //允许的其他文件上传目录
        $filePath = isset($config['file_path']) ? $config['file_path'] : $defConf['file_path'];
        if (substr($filePath, 0, strlen('.')) === '.') {
            $filePath = ltrim($filePath, '.');
        }
        $filePath = ltrim(ltrim($filePath, '\\'), '/');
        $this->filePath = '.' . DIRECTORY_SEPARATOR . $filePath;
        //允许的其他文件MIME类型
        $this->fileMimes = isset($config['file_mimes']) ? $config['file_mimes'] : $defConf['file_mimes'];
        //允许的其他文件后缀名
        $this->fileExt = isset($config['file_ext']) ? $config['file_ext'] : $defConf['file_ext'];
        //允许的其他文件大小
        $this->fileSize = isset($config['file_size']) ? $config['file_size'] : $defConf['file_size'];
        //路径过滤
        $this->filter = isset($config['filter']) ? $config['filter'] : $defConf['filter'];
        //地址前缀
        $this->httpHost = isset($config['http_host']) ? $config['http_host'] : $defConf['http_host'];
        //oss配置
        $this->ossConfig = isset($config['oss_config']) ? $config['oss_config'] : $defConf['oss_config'];
        //七牛配置
        $this->qiNiuConfig = isset($config['qi_niu_config']) ? $config['qi_niu_config'] : $defConf['qi_niu_config'];
        //提示信息
        $this->tipsMessage = isset($config['tips_message']) ? $config['tips_message'] : $defConf['tips_message'];
        //命名方式 md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
        if (isset($config['give_name']) && in_array($config['give_name'], ['md5', 'dateMd5', 'original', 'dateOriginal'])) {
            $this->giveName = $config['give_name'];
        } else {
            $this->giveName = $defConf['give_name'];
        }
    }

    /**
     * @return mixed
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param mixed $multiple
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
    }

    /**
     * @return mixed|string
     */
    public function getUploadServer()
    {
        return $this->uploadServer;
    }

    /**
     * @param mixed|string $uploadServer
     */
    public function setUploadServer($uploadServer)
    {
        $this->uploadServer = $uploadServer;
    }

    /**
     * @return mixed
     */
    public function getTipsMessage()
    {
        return $this->tipsMessage;
    }

    /**
     * @param mixed $tipsMessage
     */
    public function setTipsMessage($tipsMessage)
    {
        $this->tipsMessage = $tipsMessage;
    }

    /**
     * @return mixed
     */
    public function getFileObjArr()
    {
        return $this->fileObjArr;
    }

    /**
     * @param mixed $fileObjArr
     */
    public function setFileObjArr($fileObjArr)
    {
        $this->fileObjArr = $fileObjArr;
    }

    /**
     * @return mixed
     */
    public function getFileObj()
    {
        return $this->fileObj;
    }

    /**
     * @param mixed $fileObj
     */
    public function setFileObj($fileObj)
    {
        $this->fileObj = $fileObj;
    }

    /**
     * @return mixed
     */
    public function getOss()
    {
        return $this->oss;
    }

    /**
     * @param mixed $oss
     */
    public function setOss($oss)
    {
        $this->oss = $oss;
    }

    /**
     * @return mixed
     */
    public function getQn()
    {
        return $this->qn;
    }

    /**
     * @param mixed $qn
     */
    public function setQn($qn)
    {
        $this->qn = $qn;
    }

    /**
     * @return mixed
     */
    public function getGiveName()
    {
        return $this->giveName;
    }

    /**
     * @param mixed $giveName
     */
    public function setGiveName($giveName)
    {
        $this->giveName = $giveName;
    }

    /**
     * @return mixed
     */
    public function getMimes()
    {
        return $this->mimes;
    }

    /**
     * @param mixed $mimes
     */
    public function setMimes($mimes)
    {
        $this->mimes = $mimes;
    }

    /**
     * @return mixed
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @param mixed $tempDir
     */
    public function setTempDir($tempDir)
    {
        if (empty($tempDir)) {
            $this->tempDir = false;
        } else {
            if (substr($tempDir, 0, strlen('.')) === '.') {
                $tempDir = ltrim($tempDir, '.');
            }
            $tempDir = ltrim(ltrim($tempDir, '\\'), '/');
            $this->tempDir = '.' . DIRECTORY_SEPARATOR . $tempDir;
        }
    }

    /**
     * @return mixed
     */
    public function getImgPath()
    {
        return $this->imgPath;
    }

    /**
     * @param $filePath
     * @param false $absolute
     */
    public function setImgPath($filePath, $absolute = false)
    {
        if ($absolute) {
            $this->imgPath = $filePath;
        } else {
            if (substr($filePath, 0, strlen('.')) === '.') {
                $filePath = ltrim($filePath, '.');
            }
            $filePath = ltrim(ltrim($filePath, '\\'), '/');
            $this->imgPath = '.' . DIRECTORY_SEPARATOR . $filePath;
        }
    }

    /**
     * @return mixed
     */
    public function getImgMimes()
    {
        return $this->imgMimes;
    }

    /**
     * @param mixed $imgMimes
     */
    public function setImgMimes($imgMimes)
    {
        $this->imgMimes = $imgMimes;
    }

    /**
     * @return mixed
     */
    public function getImgExt()
    {
        return $this->imgExt;
    }

    /**
     * @param mixed $imgExt
     */
    public function setImgExt($imgExt)
    {
        $this->imgExt = $imgExt;
    }

    /**
     * @return mixed
     */
    public function getImgSize()
    {
        return $this->imgSize;
    }

    /**
     * @param mixed $imgSize
     */
    public function setImgSize($imgSize)
    {
        $this->imgSize = $imgSize;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param $filePath
     * @param false $absolute
     */
    public function setFilePath($filePath, $absolute = false)
    {
        if ($absolute) {
            $this->filePath = $filePath;
        } else {
            if (substr($filePath, 0, strlen('.')) === '.') {
                $filePath = ltrim($filePath, '.');
            }
            $filePath = ltrim(ltrim($filePath, '\\'), '/');
            $this->filePath = '.' . DIRECTORY_SEPARATOR . $filePath;
        }
    }

    /**
     * @return mixed
     */
    public function getFileMimes()
    {
        return $this->fileMimes;
    }

    /**
     * @param mixed $fileMimes
     */
    public function setFileMimes($fileMimes)
    {
        $this->fileMimes = $fileMimes;
    }

    /**
     * @return mixed
     */
    public function getFileExt()
    {
        return $this->fileExt;
    }

    /**
     * @param mixed $fileExt
     */
    public function setFileExt($fileExt)
    {
        $this->fileExt = $fileExt;
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param mixed $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param mixed $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param mixed $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function getHttpHost()
    {
        return $this->httpHost;
    }

    /**
     * @param mixed $httpHost
     */
    public function setPathPrefix($httpHost)
    {
        $this->httpHost = $httpHost;
    }

    /**
     * @return mixed
     */
    public function getOssConfig()
    {
        return $this->ossConfig;
    }

    /**
     * @param mixed $ossConfig
     */
    public function setOssConfig($ossConfig)
    {
        $this->ossConfig = $ossConfig;
    }

    /**
     * @return mixed
     */
    public function getQiNiuConfig()
    {
        return $this->qiNiuConfig;
    }

    /**
     * @param mixed $qiNiuConfig
     */
    public function setQiNiuConfig($qiNiuConfig)
    {
        $this->qiNiuConfig = $qiNiuConfig;
    }

    /**
     * @desc:移动文件
     * @param $fileObj
     * @param $path
     * @return mixed
     * @throws Exception
     * @author: lyh
     * @date: 2021/6/21
     * @time: 6:17 下午
     */
    public function move($fileObj, $path)
    {
        /* 移动文件 */
        $path = rtrim(rtrim($path, '\\'), '/');
        list($filePath, $fileName) = $this->getFileName($fileObj);
        Util::mkDirs($path . $filePath);
        $savePath = $path . $filePath . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($fileObj->getTmpName(), $savePath)) {
            throw new Exception(Config::get('tips_message', 'upload_write_error'));
        }
        $fileObj->setSaveName($savePath);
        $fileObj->setResultPath($this->getResPath($fileObj));
        return $fileObj;
    }

    /**
     * @desc:获取文件名称 带 / 开头
     * @param $fileObj
     * @return array
     * @author: lyh
     * @date: 2021/6/21
     * @time: 6:17 下午
     */
    public function getFileName($fileObj)
    {
        $filePath = '';
        $ext = $fileObj->getExt();
        switch ($this->giveName) {
            case 'dateMd5':
                $filePath = DIRECTORY_SEPARATOR . date('Ymd');
                $fileName = md5(serialize($fileObj) . rand(1000, 9999)) . '.' . $ext;
                break;
            case 'original':
                $fileName = $fileObj->name;
                break;
            case 'dateOriginal':
                $filePath = DIRECTORY_SEPARATOR . date('Ymd');
                $fileName = $fileObj->name;
                break;
            default:
                $fileName = md5(serialize($fileObj) . rand(1000, 9999)) . '.' . $ext;
        }
        $filePath = str_replace("\\", "/", $filePath);
        return [$filePath, $fileName];
    }

    /**
     * @desc:返回路径处理
     * @param $fileObj
     * @return string
     * @throws Exception
     * @author: lyh
     * @date: 2021/6/21
     * @time: 3:31 下午
     */
    protected function getResPath($fileObj)
    {
        $filePath = $fileObj->getSaveName();
        if (substr($filePath, 0, strlen('.')) === '.') {
            $filePath = ltrim($filePath, '.');
        }
        if (!empty($this->filter) && is_array($this->filter)) {
            foreach ($this->filter as $search => $replace) {
                $filePath = str_ireplace($search, $replace, $filePath);
            }
        }
        return $this->httpHost . $filePath;
    }

    /**
     * @desc:获取根目录下文件绝对路径
     * @param $path
     * @return array|mixed|string|string[]
     * @author: lyh
     * @date: 2021/6/21
     * @time: 4:39 下午
     */
    public function absolutePath($path)
    {
        if (is_array($path)) {
            foreach ($path as $key => $url) {
                if (substr($url, 0, strlen('.')) !== '.') {
                    $domain = $this->getDomain($url);
                    $path[$key] = str_ireplace($domain, '', $url);
                } else {
                    $path[$key] = ltrim($url, '.');;
                }
            }
        } else {
            if (substr($path, 0, strlen('.')) !== '.') {
                $domain = $this->getDomain($path);
                $path = str_ireplace($domain, '', $path);
            } else {
                $path = ltrim($path, '.');
            }
        }
        return $path;
    }

    /**
     * @desc:获取文件域名
     * @param $path
     * @return string
     * @author: lyh
     * @date: 2021/6/21
     * @time: 4:46 下午
     */
    public function getDomain($path)
    {
        $http = '';
        $path = str_ireplace('\\', '/', $path);
        if (strpos($path, 'https://') === 0) {
            $http = 'https://';
            $path = str_ireplace('https://', '', $path);
        } else if (strpos($path, 'http://') === 0) {
            $http = 'http://';
            $path = str_ireplace('http://', '', $path);
        } else {
            return '';
        }
        $pathArr = explode('/', $path);
        return $http . reset($pathArr);
    }
}