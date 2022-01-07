<?php


namespace EasyUpload\library;


use EasyUpload\interfaces\Upload;
use EasyUpload\service\AliOssService;
use EasyUpload\struct\ConfigStruct;
use EasyUpload\struct\EasyResultStruct;
use EasyUpload\tool\Util;

class OssUpload extends BaseUpload implements Upload
{
    public function __construct(?ConfigStruct $config = null)
    {
        parent::__construct($config);
    }

    public function imgUpload(string $formName = 'file'): EasyResultStruct
    {
        $this->setFileType('image');
        return $this->upload($formName);
    }

    public function fileUpload(string $formName = 'file'): EasyResultStruct
    {
        $this->setFileType('file');
        return $this->upload($formName);
    }

    public function moveTmpToPath($path, bool $img = true, bool $absolutePath = false)
    {
        $resData = [];
        if (is_array($path)) {
            foreach ($path as $p) {
                $resData[] = $this->moveFileToPath($p, $img, $absolutePath);
            }
            return $resData;
        } else {
            return $this->moveFileToPath($path, $img, $absolutePath);
        }
    }

    private function moveFileToPath($path, $img = true, $absolutePath = false)
    {
        $temp = trim(trim($this->tempDir, '.'), '/\\');
        if (empty($path) || strpos($path, $temp) === false) {
            $savePath = $path;
        } else {
            $oss = AliOssService::getInstance();
            if ($img) {
                $typePath = trim(trim(str_ireplace('\\', '/', $this->imgPath), '.'), '/\\');
            } else {
                $typePath = trim(trim(str_ireplace('\\', '/', $this->filePath), '.'), '/\\');
            }
            $savePath = str_ireplace($temp, $typePath, $path);
            $oss->moveFile($path, $savePath);
        }
        if (!$absolutePath) {
            $savePath = $this->absolutePath($savePath);
        }
        return $savePath;
    }

    public function httpPath($path, $suffix = '', $emptyRes = '')
    {
        return AliOssService::getInstance()->httpPath($path, $suffix, $emptyRes);
    }

    public function del($path): bool
    {
        return AliOssService::getInstance()->delFile($path);
    }

    /**
     * @desc:上传文件处理
     * @param $formName
     * @return EasyResultStruct
     * @date: 2021/6/21
     * @time: 2:13 下午
     */
    private function upload($formName): EasyResultStruct
    {
        $err = $this->BaseUpload($formName);
        if (empty($err)) {
            $result = $this->handle();
        } else {
            $result = new EasyResultStruct(false, '', $err);
        }
        return $result;
    }

    /**
     * @desc:开始上传
     * @return EasyResultStruct
     * @date: 2021/6/21
     * @time: 2:13 下午
     */
    private function handle(): EasyResultStruct
    {
        // 初始化参数
        $ext = $this->imgExt;
        $mime = $this->imgMimes;
        $size = $this->imgSize;
        $path = $this->imgPath;
        if ($this->fileType == 'file') {
            $ext = $this->fileExt;
            $mime = $this->fileMimes;
            $size = $this->fileSize;
            $path = $this->filePath;
        }
        if (!empty($this->tempDir)) {
            $path = str_replace('\\', '/', $this->tempDir);
            if (substr($path, 0, 1) == '.') {
                $path = substr($path, 1);
            }
            $path = trim(trim($path, '\\'), '/');
        }
        $path = ltrim($this->absolutePath($path), '/');
        $result = new EasyResultStruct(true, '', '');
        if ($this->multiple) {
            //批量上传
            $successArr = [];
            $errArr = [];
            foreach ($this->fileObjArr as $file) {
                try {
                    Util::checkExt($file, $ext);
                    Util::checkSize($file, $size);
                    if ($this->mimes) {
                        Util::checkMime($file, $mime);
                    }
                    list($filePath, $fileName) = $this->getFileName($file);
                    $savePath = $path . $filePath . '/' . $fileName;
                    $ossRes = AliOssService::getInstance()->uploadFile($savePath, $file->getTmpName());
                    $successArr[] = $ossRes['url'];
                } catch (\Exception $e) {
                    $errArr[] = $e->getMessage();
                    $result->setStatus(false);
                }
            }
            $result->setSuccessArr($successArr);
            $result->setErrorArr($errArr);
        } else {
            //单文件上传
            try {
                Util::checkExt($this->fileObj, $ext);
                Util::checkSize($this->fileObj, $size);
                if ($this->mimes) {
                    Util::checkMime($this->fileObj, $mime);
                }
                list($filePath, $fileName) = $this->getFileName($this->fileObj);
                $savePath = $path . $filePath . '/' . $fileName;
                $ossRes = AliOssService::getInstance()->uploadFile($savePath, $this->fileObj->getTmpName());
                $result->setSuccess($ossRes['url']);
            } catch (\Exception $e) {
                $result->setError($e->getMessage());
                $result->setStatus(false);
            }
        }
        return $result;
    }
}