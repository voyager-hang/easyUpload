<?php
const DS = DIRECTORY_SEPARATOR;
return [
    //Mimes验证
    'mimes' => false,
    //上传oss
    'oss' => false,
    //上传七牛
    'qn' => false,
    //临时目录
    'temp_dir' => DS . 'temp',
    //图片文件上传目录
    'img_path' => DS . 'uploads' . DS . 'images',
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
    'file_path' => DS . 'uploads' . DS . 'file',
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
    //填写会拼接在地址前面返回 该字符串不会被过滤
    'path_prefix' => '',

    // oss 配置
    'oss_config' => [
        'key_id' => '',  // 您的Access Key ID
        'key_secret' => '',  // 您的Access Key Secret
        'network_protocol' => 'http',  // 阿里云oss 外网协议 默认http
        'endpoint' => '',  // 阿里云oss 外网地址endpoint 不带Bucket名称
        'bucket' => '',  // Bucket名称
        'http_host' => '', // 自定义域名
    ],

    // 七牛 配置
    'qi_niu_config' => [
        'access_key' => '',// 您的Access Key
        'secret_key' => '',// 您的Secret Key
        'bucket' => '',// Bucket名称
        'http_host' => '',// 自定义域名
    ]
];