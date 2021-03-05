<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('PAY_URL', __DIR__ . '/');  //定义根路径
define('IMG_UPLOADS', __DIR__ . '/public/static/uploads/');  //文件上传位置
define('GOODS_IMG_UPLOADS', __DIR__ . '/public/static/uploads/goods_thumb/'); //商品主图保存位置
define('GOODS_PHOTO_UPLOADS', __DIR__ . '/public/static/uploads/goods_photo/'); //商品相册保存位置

define('UEDITOR',__DIR__.'/../ueditor');	//编辑器保存图片位置
define('HTTP_UEDITOR','/ueditor');	//编辑器保存图片位置
define('DEL_UEDITOR',__DIR__.'/../.');  //删除编辑器图片定位
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';