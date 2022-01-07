<?php

namespace EasyUpload\library;

use EasyUpload\config\Config;
use EasyUpload\interfaces\Upload;
use EasyUpload\struct\ConfigStruct;
use EasyUpload\struct\EasyResultStruct;
use EasyUpload\tool\Util;
use Exception;

class SysUpload extends BaseUpload implements Upload
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

    /**
     * @param $pathData
     * @param bool $img
     * @param false $absolutePath
     * @return array|string
     * @date: 2021/6/23
     * @time: 3:50 下午
     */
    public function moveTmpToPath($pathData, bool $img = true, bool $absolutePath = false)
    {
        $savePath = [];
        if (is_array($pathData)) {
            foreach ($pathData as $path) {
                $filePath = $path;
                if (!$absolutePath) {
                    $filePath = '.' . $this->absolutePath($path);
                }
                try {
                    $savePath[] = $this->moveFileToPath($filePath, $img);
                } catch (Exception $e) {
                    return $pathData;
                }
            }
        } else {
            $filePath = $pathData;
            if (!$absolutePath) {
                $filePath = '.' . $this->absolutePath($pathData);
            }
            try {
                $savePath = $this->moveFileToPath($filePath, $img);
            } catch (Exception $e) {
                return $pathData;
            }
        }
        return $savePath;
    }

    /**
     * @param $filePath
     * @param $img
     * @return array|string
     * @throws Exception
     * @date: 2021/6/23
     * @time: 3:49 下午
     */
    private function moveFileToPath($filePath, $img)
    {
        $formalPath = $this->filePath;
        if ($img) {
            $formalPath = $this->imgPath;
        }
        if (!is_file($filePath)) {
            throw new Exception(Config::get('tips_message', 'move_empty_file'));
        }
        $rmTempPath = str_ireplace(trim($this->tempDir, '.'), '', $filePath);
        $rmTempPath = trim($rmTempPath, '.');
        $savePath = rtrim($formalPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($rmTempPath, DIRECTORY_SEPARATOR);
        Util::mkDirs(dirname($savePath));
        if (!copy($filePath, $savePath)) {
            throw new Exception(Config::get('tips_message', 'upload_write_error'));
        }
        $this->del($filePath);
        return $this->absolutePath($savePath);
    }

    public function httpPath($path, $suffix = '', $emptyRes = '')
    {
        if (is_array($path)) {
            foreach ($path as $k => $v) {
                $path[$k] = $v . $suffix;
            }
        } else {
            $path .= $suffix;
        }
        return $path;
    }

    public function del($path): bool
    {
        $path = $this->absolutePath($path);
        return $this->delFile($path);
    }

    private function delFile($paths): bool
    {
        $pathArr = [];
        if (is_array($paths)) {
            $pathArr = $paths;
        } else {
            $pathArr[] = $paths;
        }
        foreach ($pathArr as $path) {
            $filePath = '.' . $path;
            if (is_file($filePath)) {
                try {
                    unlink($filePath);
                } //捕获异常
                catch (\Exception $e) {
                }
            }
        }
        return true;
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
        $savePath = $this->imgPath;
        if ($this->fileType == 'file') {
            $ext = $this->fileExt;
            $mime = $this->fileMimes;
            $size = $this->fileSize;
            $savePath = $this->filePath;
        }
        if (!empty($this->tempDir)) {
            $savePath = $this->tempDir;
        }
        $result = new EasyResultStruct(true, '', '');
        if ($this->multiple) {
            $successArr = [];
            $errArr = [];
            //批量上传
            foreach ($this->fileObjArr as $file) {
                try {
                    Util::checkExt($file, $ext);
                    Util::checkSize($file, $size);
                    if ($this->mimes) {
                        Util::checkMime($file, $mime);
                    }
                    $newFile = $this->move($file, $savePath);
                    $successArr[] = $newFile->getResultPath();
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
                $newFile = $this->move($this->fileObj, $savePath);
                $result->setSuccess($newFile->getResultPath());
            } catch (\Exception $e) {
                $result->setError($e->getMessage());
                $result->setStatus(false);
            }
        }
        return $result;
    }
}