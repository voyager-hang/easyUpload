<?php

namespace EasyUpload\tool;

use EasyUpload\config\Config;
use EasyUpload\file\File;
use EasyUpload\struct\FileArrStruct;
use Exception;

class Util
{
    // objHandle 多文件上传处理成单文件数组
    public static function objHandle($fileMsg): array
    {
        $multiple = true;
        $fileArray = [];
        if (empty($fileMsg)) {
            return array(false, $fileArray);
        }
        if (is_array($fileMsg['name'])) {
            foreach ($fileMsg['name'] as $k => $fn) {
                if (strpos($fn, '.') !== false) {
                    $ext = substr(strrchr($fn, '.'), 1);
                } else {
                    $ext = '';
                }
                $fileObj = new File();
                $fileObj->setName($fn);
                $fileObj->setType($fileMsg['type'][$k]);
                $fileObj->setTmpName($fileMsg['tmp_name'][$k]);
                $fileObj->setError($fileMsg['error'][$k]);
                $fileObj->setSize($fileMsg['size'][$k]);
                $fileObj->setSizeKb($fileObj->getSize() / 1024);
                $fileObj->setSizeMb($fileObj->getSizeKb() / 1024);
                $fileObj->setExt($ext);
                $imgInfo = getimagesize($fileObj->getTmpName());
                if (!empty($imgInfo['0']) && !empty($imgInfo['1'])) {
                    $fileObj->setWidth($imgInfo['0']);
                    $fileObj->setHeight($imgInfo['1']);
                }
                $fileArray[] = $fileObj;
            }
            $fileRes = new FileArrStruct($fileArray);
        } else {
            $multiple = false;
            if (strpos($fileMsg['name'], '.') !== false) {
                $ext = substr(strrchr($fileMsg['name'], '.'), 1);
            } else {
                $ext = '';
            }
            $fileObj = new File();
            $fileObj->setName($fileMsg['name']);
            $fileObj->setType($fileMsg['type']);
            $fileObj->setTmpName($fileMsg['tmp_name']);
            $fileObj->setError($fileMsg['error']);
            $fileObj->setSize($fileMsg['size']);
            $fileObj->setSizeKb($fileObj->getSize() / 1024);
            $fileObj->setSizeMb($fileObj->getSizeKb() / 1024);
            $fileObj->setExt($ext);
            $imgInfo = getimagesize($fileObj->getTmpName());
            if (!empty($imgInfo['0']) && !empty($imgInfo['1'])) {
                $fileObj->setWidth($imgInfo['0']);
                $fileObj->setHeight($imgInfo['1']);
            }
            $fileRes = $fileObj;
        }
        // [是否多文件，文件对象]
        return array($multiple, $fileRes);
    }

    /** 验证文件大小
     * @throws Exception
     */
    public static function checkSize(File $file, $size)
    {
        if ($file->getSizeKb() > $size) {
            throw new Exception(Config::get('tips_message', 'oversize_size'));
        }
    }

    /** 验证文件Mime
     * @throws Exception
     */
    public static function checkMime(File $file, $mimes)
    {
        if (!is_array($mimes)) {
            throw new Exception(Config::get('tips_message', 'mime_error'));
        }
        if (!in_array($file->getType(), $mimes)) {
            throw new Exception(Config::get('tips_message', 'mime_not'));
        }
    }

    /** 验证文件后缀名
     * @throws Exception
     */
    public static function checkExt(File $file, $extArr)
    {
        $ext = $file->getExt();
        if (!is_array($extArr)) {
            throw new Exception(Config::get('tips_message', 'ext_error'));
        }
        if (!in_array($ext, $extArr)) {
            throw new Exception(Config::get('tips_message', 'ext_not'));
        }
    }

    /**递归的生成目录
     * @name:
     * @desc:
     * @param $dir
     * @return bool
     * @date: 2021/6/21
     * @time: 4:07 下午
     */
    public static function mkDirs($dir): bool
    {
        return is_dir($dir) || self::mkDirs(dirname($dir)) && mkdir($dir);
    }

    public static function dump($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>' . PHP_EOL . PHP_EOL;
    }
}
