GitHub:[整合阿里云Oss，七牛云上传，兼容Thinkphp5.1](https://github.com/mygithub-hang/easyUpload)

+ 文件大小验证
+ 文件Mime类型验证
+ 文件后缀名验证
+ 批量删除
+ 批量上传
+ 临时目录
+ 临时目录替换
+ 获取可访问地址


> V1.*.*版本 运行环境要求PHP5.6+，七牛sdk7.3+，ossSdk2.3+。
>
> V2+版本 运行环境要求PHP7.4+，七牛sdk7.3+，ossSdk2.3+。


>安装

使用composer安装

```php
composer require yuanhang/easy-upload
```

>使用

设置配置

```php
\EasyUpload\EasyUpload::setConfig(array $config);
\EasyUpload\EasyUpload::Instance();

Thinkphp5.1可在配置目录建EasyUpload.php

其他框架也可以自定义配置文件
使用 ：\EasyUpload\EasyUpload::getConfigPath(); 获取配置文件位置
然后根据位置新建文件即可
示例文件：/src/example/Config.php
```

>获取实例

```php
$upload = \EasyUpload\EasyUpload::Instance();
```

>上传图片
```php
$res = $upload->imgUpload('form_name');
```

>上传文件
```php
$res = $upload->fileUpload('form_name');
```

>获取可访问地址
```php
$upload->httpPath('/uploads/images/a.png');
$upload->httpPath('http://www.a.com/uploads/a.png');
```

>把文件从临时目录移动到正式目录 (文件地址,是否图片,是否绝对路径)
```php
$upload->moveTmpToPath('http://www.d.com/temp/a.png', true, false);
$upload->httpPath('/temp/a.png',true,true);
```

>删除文件
```php
$upload->del('http://www.d.com/temp/a.png');
$upload->del('/temp/a.png');
$upload->del(['/temp/a.png','http://www.d.com/temp/a.png']);
```

# 返回值

>单文件上传成功
```php
 {
    "status": true,
    "success": "/temp/20210622/a.gif",
    "error": ""
 }
```

>单文件上传失败
```php
 {
    "status": false,
    "success": "",
    "error": "上传文件超过允许的最大值!"
 }
```

>多文件上传成功
```php
 {
    "status": true,
    "success": ["/temp/20210622/a.gif", "/temp/20210622/b.png"],
    "error": []
 }
```

>多文件上传失败
```php
 {
    "status": false,
    "success": [],
    "error": ["上传文件超过允许的最大值!", "上传文件超过允许的最大值!"]
 }
```

## 其他方法均以抛出异常方式返回

## 目录结构

```php
src                      项目根目录
├─config                 默认配置目录
│  ├─Config.php          默认配置文件
│
├─example                示例目录
│  ├─Config.php          配置示例文件
│  ├─Upload.php          使用示例文件
│
├─file                   文件对象目录
│  ├─File.php            文件类
│
├─interfaces             接口目录
│  ├─Upload.php          上传接口
│
├─library                上传类目录
│  ├─BaseUpload.php      上传基类
│  ├─OssUpload.php       阿里云oss上传类
│  ├─QiNiuUpload.php     七牛云上传类
│  ├─SysUpload.php       系统上传类
│
├─service                第三方服务整合类
│  ├─AliOssService.php   阿里云Oss上传封装
│  ├─QnService.php       七牛云上传封装
│
├─tool                   工具类目录
│  ├─Util.php            基础工具类
│
├─EasyUpload.php         项目入口工厂类 工厂模式获取实例
│
```
## 配置文件示例
```php
<?php
return [
    //Mimes验证
    'mimes' => false,
    //上传oss(阿里云oss)，server(服务器 默认)，qn(七牛)
    'upload_server' => 'server',
    //命名方式  md5(md5), dateMd5(日期md5 默认) , original(原名) , dateOriginal(日期原名)
    'give_name' => 'dateMd5',
    //临时目录 false 关闭
    'temp_dir' => 'temp',
    //图片文件上传目录
    'img_path' => 'uploads' . DIRECTORY_SEPARATOR . 'images',
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
    'file_path' => 'uploads' . DIRECTORY_SEPARATOR . 'file',
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
```
