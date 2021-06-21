<?php
namespace EasyUpload\library;

use EasyUpload\interfaces\Upload;

class SysUpload implements Upload
{
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