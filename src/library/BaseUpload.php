<?php


namespace EasyUpload\library;


use EasyUpload\config\Config;
use EasyUpload\file\File;
use EasyUpload\struct\ConfigStruct;
use EasyUpload\struct\EasyResultStruct;
use EasyUpload\struct\FileArrStruct;
use EasyUpload\struct\OssConfigStruct;
use EasyUpload\struct\QnConfigStruct;
use EasyUpload\tool\Util;
use Exception;

class BaseUpload
{
    // 是否多文件上传
    protected bool $multiple;
    // 当前配置文件
    protected ConfigStruct $config;
    // 当前上传的文件对象
    protected File $fileObj;
    // 当前上传的文件对象数组
    protected FileArrStruct $fileObjArr;
    //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
    protected string $uploadServer;
    //命名方式  md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
    protected string $giveName;
    //是否开启mimes验证
    protected bool $mimes;
    //临时文件夹
    protected string $tempDir;
    //允许的图片文件上传目录
    protected string $imgPath;
    //允许的图片文件MIME类型
    protected array $imgMimes;
    //允许的图片文件后缀名
    protected array $imgExt;
    //允许的图片文件大小
    protected int $imgSize;
    //允许的其他文件上传目录
    protected string $filePath;
    //允许的其他文件MIME类型
    protected array $fileMimes;
    //允许的其他文件后缀名
    protected array $fileExt;
    //允许的其他文件大小
    protected int $fileSize;
    //文件类型 image|file
    protected string $fileType;
    //路径过滤
    protected array $filter;
    //自定义域名
    protected string $httpHost;
    //oss配置
    protected OssConfigStruct $ossConfig;
    //七牛配置
    protected QnConfigStruct $qiNiuConfig;
    //提示信息
    protected array $tipsMessage;

    public function __construct(?ConfigStruct $config = null)
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
                    Config::setCof($cof);
                    $config = Config::def();
                }
            }
        }
        $this->config = $config;
        //上传访问 oss(阿里云oss)，server(服务器 默认)，qn(七牛)
        $uploadServer = $config->getUploadServer();
        if (in_array($uploadServer, ['oss', 'server', 'qn'])) {
            $this->uploadServer = $uploadServer;
        } else {
            $this->uploadServer = 'server';
        }
        //是否开启mimes验证
        $this->mimes = $config->isMimes();
        //临时文件夹
        $tempDir = $config->getTempDir();
        if (substr($tempDir, 0, strlen('.')) === '.') {
            $tempDir = ltrim($tempDir, '.');
        }
        $tempDir = ltrim(ltrim($tempDir, '\\'), '/');
        $this->tempDir = '.' . DIRECTORY_SEPARATOR . $tempDir;
        //允许的图片文件上传目录
        $imgPath = $config->getImgPath();
        if (substr($imgPath, 0, strlen('.')) === '.') {
            $imgPath = ltrim($imgPath, '.');
        }
        $imgPath = ltrim(ltrim($imgPath, '\\'), '/');
        $this->imgPath = '.' . DIRECTORY_SEPARATOR . $imgPath;
        //允许的图片文件MIME类型
        $this->imgMimes = $config->getImgMimes();
        //允许的图片文件后缀名
        $this->imgExt = $config->getImgExt();
        //允许的图片文件大小
        $this->imgSize = $config->getImgSize();
        //允许的其他文件上传目录
        $filePath = $config->getFilePath();
        if (substr($filePath, 0, strlen('.')) === '.') {
            $filePath = ltrim($filePath, '.');
        }
        $filePath = ltrim(ltrim($filePath, '\\'), '/');
        $this->filePath = '.' . DIRECTORY_SEPARATOR . $filePath;
        //允许的其他文件MIME类型
        $this->fileMimes = $config->getFileMimes();
        //允许的其他文件后缀名
        $this->fileExt = $config->getFileExt();
        //允许的其他文件大小
        $this->fileSize = $config->getFileSize();
        //路径过滤
        $this->filter = $config->getFilter();
        //地址前缀
        $this->httpHost = $config->getHttpHost();
        //oss配置
        $this->ossConfig = $config->getOssConfig();
        //七牛配置
        $this->qiNiuConfig = $config->getQiNiuConfig();
        //提示信息
        $this->tipsMessage = $config->getTipsMessage();
        //命名方式 md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
        if (in_array($config->getGiveName(), ['md5', 'dateMd5', 'original', 'dateOriginal'])) {
            $this->giveName = $config->getGiveName();
        } else {
            $this->giveName = $defConf->getGiveName();
        }
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     */
    public function setMultiple(bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    /**
     * @return ConfigStruct|null
     */
    public function getConfig(): ?ConfigStruct
    {
        return $this->config;
    }

    /**
     * @param ConfigStruct|null $config
     */
    public function setConfig(?ConfigStruct $config): void
    {
        $this->config = $config;
    }

    /**
     * @return File
     */
    public function getFileObj(): File
    {
        return $this->fileObj;
    }

    /**
     * @param File $fileObj
     */
    public function setFileObj(File $fileObj): void
    {
        $this->fileObj = $fileObj;
    }

    /**
     * @return FileArrStruct
     */
    public function getFileObjArr(): FileArrStruct
    {
        return $this->fileObjArr;
    }

    /**
     * @param FileArrStruct $fileObjArr
     */
    public function setFileObjArr(FileArrStruct $fileObjArr): void
    {
        $this->fileObjArr = $fileObjArr;
    }

    /**
     * @return string
     */
    public function getUploadServer(): string
    {
        return $this->uploadServer;
    }

    /**
     * @param string $uploadServer
     */
    public function setUploadServer(string $uploadServer): void
    {
        $this->uploadServer = $uploadServer;
    }

    /**
     * @return string
     */
    public function getGiveName(): string
    {
        return $this->giveName;
    }

    /**
     * @param string $giveName
     */
    public function setGiveName(string $giveName): void
    {
        $this->giveName = $giveName;
    }

    /**
     * @return bool
     */
    public function isMimes(): bool
    {
        return $this->mimes;
    }

    /**
     * @param bool $mimes
     */
    public function setMimes(bool $mimes): void
    {
        $this->mimes = $mimes;
    }

    /**
     * @return array|string[]
     */
    public function getImgMimes(): array
    {
        return $this->imgMimes;
    }

    /**
     * @param array|string[] $imgMimes
     */
    public function setImgMimes(array $imgMimes): void
    {
        $this->imgMimes = $imgMimes;
    }

    /**
     * @return array|string[]
     */
    public function getImgExt(): array
    {
        return $this->imgExt;
    }

    /**
     * @param array|string[] $imgExt
     */
    public function setImgExt(array $imgExt): void
    {
        $this->imgExt = $imgExt;
    }

    /**
     * @return int
     */
    public function getImgSize(): int
    {
        return $this->imgSize;
    }

    /**
     * @param int $imgSize
     */
    public function setImgSize(int $imgSize): void
    {
        $this->imgSize = $imgSize;
    }

    /**
     * @return array|string[]
     */
    public function getFileMimes(): array
    {
        return $this->fileMimes;
    }

    /**
     * @param array|string[] $fileMimes
     */
    public function setFileMimes(array $fileMimes): void
    {
        $this->fileMimes = $fileMimes;
    }

    /**
     * @return array|string[]
     */
    public function getFileExt(): array
    {
        return $this->fileExt;
    }

    /**
     * @param array|string[] $fileExt
     */
    public function setFileExt(array $fileExt): void
    {
        $this->fileExt = $fileExt;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string
     * @deprecated Obsolete in the next version, please use getUploadType
     */
    public function getFileType(): string
    {
        return $this->getUploadType();
    }

    /**
     * @return string
     */
    public function getUploadType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     * @deprecated Obsolete in the next version, please use setUploadType
     */
    public function setFileType(string $fileType): void
    {
        $this->setUploadType($fileType);
    }

    /**
     * @param string $fileType
     */
    public function setUploadType(string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return array|string[]
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @param array|string[] $filter
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @return string
     */
    public function getHttpHost(): string
    {
        return $this->httpHost;
    }

    /**
     * @param string $httpHost
     */
    public function setHttpHost(string $httpHost): void
    {
        $this->httpHost = $httpHost;
    }

    /**
     * @return OssConfigStruct
     */
    public function getOssConfig(): OssConfigStruct
    {
        return $this->ossConfig;
    }

    /**
     * @param OssConfigStruct $ossConfig
     */
    public function setOssConfig(OssConfigStruct $ossConfig): void
    {
        $this->ossConfig = $ossConfig;
    }

    /**
     * @return QnConfigStruct
     */
    public function getQiNiuConfig(): QnConfigStruct
    {
        return $this->qiNiuConfig;
    }

    /**
     * @param QnConfigStruct $qiNiuConfig
     */
    public function setQiNiuConfig(QnConfigStruct $qiNiuConfig): void
    {
        $this->qiNiuConfig = $qiNiuConfig;
    }

    /**
     * @return array|string[]
     */
    public function getTipsMessage(): array
    {
        return $this->tipsMessage;
    }

    /**
     * @param array|string[] $tipsMessage
     */
    public function setTipsMessage(array $tipsMessage): void
    {
        $this->tipsMessage = $tipsMessage;
    }

    /**
     * @param string $tempDir
     */
    public function setTempDir(string $tempDir): void
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
     * @param string $filePath
     * @param bool $absolute
     */
    public function setImgPath(string $filePath, bool $absolute = false): void
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
     * @param string $filePath
     * @param bool $absolute
     */
    public function setFilePath(string $filePath, bool $absolute = false): void
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
     * @desc:上传文件处理
     * @param $formName
     * @return string
     * @date: 2021/6/21
     * @time: 2:13 下午
     */
    protected function BaseUpload($formName): string
    {
        $fileObj = $_FILES[$formName] ?? [];
        if (empty($fileObj) || empty($fileObj['size']) || empty($fileObj['size'][0])) {
            if ($this->fileType == 'file') {
                $tipsMsg = $this->tipsMessage['empty_file'];
            } else {
                $tipsMsg = $this->tipsMessage['empty_images'];
            }
            return $tipsMsg;
        } else {
            list($this->multiple, $resObj) = Util::objHandle($fileObj);
            if ($this->multiple) {
                $this->fileObjArr = $resObj;
            } else {
                $this->fileObj = $resObj;
            }
            return '';
        }
    }

    /**
     * @desc:移动文件
     * @param $fileObj
     * @param $path
     * @return mixed
     * @throws Exception
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
     * @date: 2021/6/21
     * @time: 6:17 下午
     */
    public function getFileName($fileObj): array
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
     * @date: 2021/6/21
     * @time: 3:31 下午
     */
    protected function getResPath($fileObj): string
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
     * @date: 2021/6/21
     * @time: 4:46 下午
     */
    public function getDomain($path): string
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