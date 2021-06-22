<?php

namespace EasyUpload\config;
const DS = DIRECTORY_SEPARATOR;
class Config
{
    private static $defCof = [
        //Mimes验证
        'mimes' => false,
        //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
        'upload_server' => 'qn',
        //命名方式  md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
        'give_name' => 'dateMd5',
        //临时目录 false 关闭
        'temp_dir' => 'temp',
        //图片文件上传目录
        'img_path' => 'uploads' . DS . 'images',
        //允许的图片文件MIME类型 空允许全部
        'img_mimes' => [
            'image/jpeg',
            'image/png',
            'image/gif'
        ],
        //允许的图片文件后缀名  空允许全部
        'img_ext' => ['jpg', 'jpeg', 'png', 'gif'],
        //允许的图片文件大小 kb
        'img_size' => 2048,
        //文件上传目录
        'file_path' => 'uploads' . DS . 'file',
        //允许的其他文件MIME类型
        'file_mimes' => [
            'application/msword',
            'pplication/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
        //允许的其他文件后缀名
        'file_ext' => ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'],
        //允许的其他文件大小
        'file_size' => 2048,

        //过滤路径中的字符串 ['\'=>'/'] 把路径中的\替换为/
        'filter' => [
            '\\' => '/',
        ],
        // 自定义域名
        'http_host' => '',

        // oss 配置
        'oss_config' => [
            'key_id' => '',  // 您的Access Key ID
            'key_secret' => '',  // 您的Access Key Secret
            'network_protocol' => 'http',  // 阿里云oss 外网协议 默认http
            'endpoint' => 'oss-cn-shenzhen.aliyuncs.com',  // 阿里云oss 外网地址endpoint 不带Bucket名称
            'bucket' => '',  // Bucket名称
            'http_host' => '', // 自定义域名
        ],

        // 七牛 配置
        'qi_niu_config' => [
            'access_key' => '',// 您的Access Key
            'secret_key' => '',// 您的Secret Key
            'bucket' => '',// Bucket名称
            'http_host' => '',// 外链域名
            'expires' => 3600 // 上传超时
        ],
        // 错误提示信息，使用配置方便制作多语言
        'tips_message' => [
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
        ]
    ];

    public static function def()
    {
        return self::$defCof;
    }

    public static function get(...$key)
    {
        $val = self::$defCof;
        foreach ($key as $v) {
            $val = $val[$v];
        }
        return $val;
    }

    public static function setCof($defCof)
    {
        self::$defCof = $defCof;
    }
}