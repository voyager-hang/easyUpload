<?php

namespace EasyUpload\interfaces;

use EasyUpload\struct\EasyResultStruct;

interface Upload
{
    // 上传图片文件 (表单控件名)
    public function imgUpload(string $formName = 'file'): EasyResultStruct;

    // 上传其他文件 (表单控件名)
    public function fileUpload(string $formName = 'file'): EasyResultStruct;

    // 文件临时目录移动到正式目录 (临时目录，是否图片，是否返回绝对路径)
    public function moveTmpToPath($path, bool $img = true, bool $absolutePath = true);

    // 获取文件可访问地址 (文件路径)
    public function httpPath($path, $suffix = '', $emptyRes = '', $host = '');

    // 删除文件 (路径、路径数组)
    public function del($path);
}