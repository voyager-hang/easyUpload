<?php

namespace EasyUpload\tool;

use EasyUpload\config\config;
use EasyUpload\file\File;
use Exception;

class Util
{
    // objHandle 多文件上传处理成单文件数组
    public static function objHandle($fileMsg)
    {
        $multiple = true;
        $fileArray = [];
        if (empty($fileMsg)) {
            return array(false, $fileArray);
        }
        if (is_array($fileMsg['name'])) {
            foreach ($fileMsg['name'] as $k => $fn) {
                $fileObj = new File();
                $fileObj->name = $fn;
                $fileObj->type = $fileMsg['type'][$k];
                $fileObj->tmpName = $fileMsg['tmp_name'][$k];
                $fileObj->error = $fileMsg['error'][$k];
                $fileObj->size = $fileMsg['size'][$k];
                $fileObj->sizeKb = $fileObj->size / 1024;
                $fileObj->sizeMb = $fileObj->sizeKb / 1024;
                $fileArray[] = $fileObj;
            }
        } else {
            $multiple = false;
            $fileObj = new File();
            $fileObj->name = $fileMsg['name'];
            $fileObj->type = $fileMsg['type'];
            $fileObj->tmpName = $fileMsg['tmp_name'];
            $fileObj->error = $fileMsg['error'];
            $fileObj->size = $fileMsg['size'];
            $fileObj->sizeKb = $fileObj->size / 1024;
            $fileObj->sizeMb = $fileObj->sizeKb / 1024;
            $fileArray = $fileObj;
        }
        // [是否多文件，文件对象]
        return array($multiple, $fileArray);
    }

    /** 验证文件大小
     * @throws Exception
     */
    public static function checkSize($file, $size)
    {
        if ($file->sizeKb > $size) {
            throw new Exception(config::get('tips_message', 'oversize_size'));
        }
    }

    /** 验证文件Mime
     * @throws Exception
     */
    public static function checkMime($file, $mimes)
    {
        if (!is_array($mimes)) {
            throw new Exception(config::get('tips_message', 'mime_error'));
        }
        if (!in_array($file->type, $mimes)) {
            throw new Exception(config::get('tips_message', 'mime_not'));
        }
    }

    /** 验证文件后缀名
     * @throws Exception
     */
    public static function checkExt($file, $extArr)
    {
        if (strpos($file->getName(), '.') !== false) {
            $ext = substr(strrchr($file->name, '.'), 1);
        } else {
            $ext = '';
        }
        if (!is_array($extArr)) {
            throw new Exception(config::get('tips_message', 'ext_error'));
        }
        if (!in_array($ext, $extArr)) {
            throw new Exception(config::get('tips_message', 'ext_not'));
        }
    }

    /**递归的生成目录
     * @name:
     * @desc:
     * @param $dir
     * @return bool
     * @author: lyh
     * @date: 2021/6/21
     * @time: 4:07 下午
     */
    public static function mkDirs($dir)
    {
        return is_dir($dir) || self::mkDirs(dirname($dir)) && mkdir($dir);
    }
}
