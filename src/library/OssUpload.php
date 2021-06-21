<?php


namespace EasyUpload\library;


use EasyUpload\interfaces\Upload;

class OssUpload extends BaseUpload implements Upload
{
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function imgUpload($formName = 'file')
    {
        // TODO: Implement imgUpload() method.
    }

    public function fileUpload($formName = 'file')
    {
        // TODO: Implement fileUpload() method.
    }

    public function moveTmpToPath($path, $img = true, $absolutePath = true)
    {
        // TODO: Implement moveTmpToPath() method.
    }

    public function httpPath($path)
    {
        // TODO: Implement httpPath() method.
    }

    public function del($path)
    {
        // TODO: Implement del() method.
    }
}