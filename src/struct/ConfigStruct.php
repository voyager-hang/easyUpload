<?php

namespace EasyUpload\struct;

/**
 * 基础配置结构
 * @method $this assert($model) static 断言
 */
class ConfigStruct
{
    //Mimes验证
    private bool $mimes = false;
    //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
    private string $uploadServer = 'server';
    //命名方式  md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
    private string $giveName = 'dateMd5';
    //临时目录 false 关闭
    private string $tempDir = 'temp';
    //图片文件上传目录
    private string $imgPath = 'uploads' . DIRECTORY_SEPARATOR . 'images';
    //允许的图片文件MIME类型 空允许全部
    private array $imgMimes = [
        'image/jpeg',
        'image/png',
        'image/gif'];
    //允许的图片文件后缀名  空允许全部
    private array $imgExt = ['jpg', 'jpeg', 'png', 'gif'];
    //允许的图片文件大小 kb
    private int $imgSize = 2048;
    //文件上传目录
    private string $filePath = 'uploads' . DIRECTORY_SEPARATOR . 'file';
    //允许的其他文件MIME类型
    private array $fileMimes = [
        'application/msword',
        'pplication/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    //允许的其他文件后缀名
    private array $fileExt = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];
    //允许的其他文件大小
    private int $fileSize = 2048;
    //过滤路径中的字符串 ['\'=>'/'] 把路径中的\替换为/
    private array $filter = [
        '\\' => '/'
    ];
    // 自定义域名
    private string $httpHost = '';
    // oss 配置
    private OssConfigStruct $ossConfig;
    // 七牛 配置
    private QnConfigStruct $qiNiuConfig;
    // 错误提示信息，使用配置方便制作多语言
    private array $tipsMessage = [
        'empty_images' => '没有图片被上传!',
        'empty_file' => '没有文件被上传!',
        'mime_error' => 'Mime配置错误!',
        'ext_error' => '后缀名配置类型错误!',
        'oversize_size' => '上传文件超过允许的最大值!',
        'mime_not' => '上传文件Mime不允许!',
        'ext_not' => '上传文件后缀名不允许!',
        'upload_write_error' => '移动文件失败',
        'move_empty_file' => '未找到源文件',
        'del_file_max_num' => '每次最多不能超过1000个',
    ];

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
     * @return string
     */
    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    /**
     * @param string $tempDir
     */
    public function setTempDir(string $tempDir): void
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return string
     */
    public function getImgPath(): string
    {
        return $this->imgPath;
    }

    /**
     * @param string $imgPath
     */
    public function setImgPath(string $imgPath): void
    {
        $this->imgPath = $imgPath;
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
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
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
     * @param $method
     * @param $args
     * @return mixed|string
     */
    public static function __callStatic($method, $args)
    {
        $res = self::callHandle($method, $args);
        if ($res == '__parent') {
            return self::__callStatic($method, $args);
        }
        return $res;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed|string
     */
    public function __call($method, $args)
    {
        $res = self::callHandle($method, $args);
        if ($res == '__parent') {
            return self::__call($method, $args);
        }
        return $res;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed|string
     */
    private static function callHandle($method, $args)
    {
        switch ($method) {
            case 'assert':
                return self::_assert(...$args);
                break;
            default:
                return '__parent';
        }
    }

    /**断言 原路返回
     * @param $data
     * @return mixed
     */
    private static function _assert($data)
    {
        return $data;
    }
}