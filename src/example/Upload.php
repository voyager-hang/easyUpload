<?php
require_once './vendor/autoload.php';
$config = [
    // ...
];
// 设置自定义配置
//\EasyUpload\EasyUpload::Instance($config);

// 获取EasyUpload 实例 非单例
//$upload = \EasyUpload\EasyUpload::Instance(true);
// 获取EasyUpload 实例 单例
$upload = \EasyUpload\EasyUpload::Instance();
// 上传图片文件 form_name 表单控件名称 如果是多文件 html里的name应为 form_name[]
$res = $upload->imgUpload('form_name');
/** 单文件上传失败
 * {
 * "status": false,
 * "success": "",
 * "error": "上传文件超过允许的最大值!"
 * }
 */
/** 多文件上传失败
 * {
 * "status": false,
 * "success": [],
 * "error": ["上传文件超过允许的最大值!", "上传文件超过允许的最大值!"]
 * }
 */
/**  单文件上传成功
 * {
 * "status": true,
 * "success": "/temp/20210622/0db734e0a9fb15c458689f222f6432e0.gif",
 * "error": ""
 * }
 */
/**  多文件上传成功
 * {
 * "status": true,
 * "success": ["/temp/20210622/0db734e0a9fb15c458689f222f6432e0.gif", "/temp/20210622/bb0dd1411394ade3888de5a5c5175f8c.png"],
 * "error": []
 * }
 */

// 上传其他文件 form_name 表单控件名称 如果是多文件 html里的name应为 form_name[]
$res = $upload->fileUpload('form_name');
//结果通图片上传

// 获取可直接访问的地址
$upload->httpPath('/uploads/images/20210621/1a2ab0be8daadb2b336b026db296de39.png'); // http://d.com/file

// 把文件从缓存目录移动到正式目录或去掉域名获取绝对路径 (文件地址,是否图片,是否绝对路径)
$upload->moveTmpToPath('http://d.com/temp/images/20210621/1a2ab0be8daadb2b336b026db296de39.png', true, false);

// 删除文件
$upload->del('http://d.com/temp/images/20210621/1a2ab0be8daadb2b336b026db296de39.png');