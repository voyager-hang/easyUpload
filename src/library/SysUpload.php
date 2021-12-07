<?php

namespace EasyUpload\library;

use EasyUpload\config\Config;
use EasyUpload\interfaces\Upload;
use EasyUpload\tool\Util;
use Exception;

class SysUpload extends BaseUpload implements Upload
{
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function imgUpload($formName = 'file')
    {
        // TODO: Implement imgUpload() method.
        $this->setFileType('image');
        return $this->upload($formName);
    }

    public function fileUpload($formName = 'file')
    {
        // TODO: Implement fileUpload() method.
        $this->setFileType('file');
        return $this->upload($formName);
    }

    /**
     * @param $pathData
     * @param bool $img
     * @param false $absolutePath
     * @return array|string
     * @author: lyh
     * @date: 2021/6/23
     * @time: 3:50 下午
     */
    public function moveTmpToPath($pathData, $img = true, $absolutePath = false)
    {
        // TODO: Implement moveTmpToPath() method.
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
     * @author: lyh
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
        $savePath = str_ireplace(trim($this->tempDir,'.'), $formalPath, $filePath);
        Util::mkDirs(dirname($savePath));
        if (!copy($filePath, $savePath)) {
            throw new Exception(Config::get('tips_message', 'upload_write_error'));
        }
        $this->del($filePath);
        return $this->absolutePath($savePath);
    }

    public function httpPath($path)
    {
        // TODO: Implement httpPath() method.
        return $path;
    }

    public function del($path)
    {
        // TODO: Implement del() method.
        $path = $this->absolutePath($path);
        return $this->delFile($path);
    }

    private function delFile($paths)
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
     * @return array
     * @author: lyh
     * @date: 2021/6/21
     * @time: 2:13 下午
     */
    private function upload($formName)
    {
        $fileObj = isset($_FILES[$formName]) ? $_FILES[$formName] : [];
        if (empty($fileObj) || empty($fileObj['size'])) {
            if ($this->fileType == 'file') {
                $tipsMsg = $this->tipsMessage['empty_file'];
            } else {
                $tipsMsg = $this->tipsMessage['empty_images'];
            }
            $result = ['status' => false, 'success' => '', 'error' => $tipsMsg];
        } else {
            list($this->multiple, $resObj) = Util::objHandle($fileObj);
            if ($this->multiple) {
                $this->fileObjArr = $resObj;
            } else {
                $this->fileObj = $resObj;
            }
            $result = $this->handle();
        }
        return $result;
    }

    /**
     * @desc:开始上传
     * @return array
     * @author: lyh
     * @date: 2021/6/21
     * @time: 2:13 下午
     */
    private function handle()
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

        $result = ['status' => true, 'success' => '', 'error' => ''];
        if ($this->multiple) {
            $result['success'] = [];
            $result['error'] = [];
            //批量上传
            foreach ($this->fileObjArr as $file) {
                try {
                    Util::checkExt($file, $ext);
                    Util::checkSize($file, $size);
                    if ($this->mimes) {
                        Util::checkMime($file, $mime);
                    }
                    $newFile = $this->move($file, $savePath);
                    $result['success'][] = $newFile->getResultPath();
                } catch (\Exception $e) {
                    $result['error'][] = $e->getMessage();
                    $result['status'] = false;
                }
            }
        } else {
            //单文件上传
            try {
                Util::checkExt($this->fileObj, $ext);
                Util::checkSize($this->fileObj, $size);
                if ($this->mimes) {
                    Util::checkMime($this->fileObj, $mime);
                }
                $newFile = $this->move($this->fileObj, $savePath);
                $result['success'] = $newFile->getResultPath();
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                $result['status'] = false;
            }
        }
        return $result;
    }
}