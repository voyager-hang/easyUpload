<?php


namespace EasyUpload\library;


use EasyUpload\interfaces\Upload;
use EasyUpload\service\AliOssService;
use EasyUpload\tool\Util;

class OssUpload extends BaseUpload implements Upload
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

    public function moveTmpToPath($path, $img = true, $absolutePath = false)
    {
        // TODO: Implement moveTmpToPath() method.
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
        // TODO: Implement httpPath() method.
        return AliOssService::getInstance()->httpPath($path, $suffix, $emptyRes);
    }

    public function del($path)
    {
        // TODO: Implement del() method.
        return AliOssService::getInstance()->delFile($path);
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
                    list($filePath, $fileName) = $this->getFileName($file);
                    $savePath = $path . $filePath . '/' . $fileName;
                    $ossRes = AliOssService::getInstance()->uploadFile($savePath, $file->getTmpName());
                    $result['success'][] = $ossRes['url'];
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
                list($filePath, $fileName) = $this->getFileName($this->fileObj);
                $savePath = $path . $filePath . '/' . $fileName;
                $ossRes = AliOssService::getInstance()->uploadFile($savePath, $this->fileObj->getTmpName());
                $result['success'] = $ossRes['url'];
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                $result['status'] = false;
            }
        }
        return $result;
    }
}