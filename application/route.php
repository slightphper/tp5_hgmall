<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::rule('cate/:id','index/Cate/index','get',['ext'=>'html|htm'],['id'=>'\d{1,3}']);
Route::rule('article/:id','index/Article/index','get',['ext'=>'html|htm'],['id'=>'\d{1,3}']);
//首页路由
Route::rule('index','index/Index/index','get',['ext'=>'html|htm']);
//品牌路由
// Route::rule('index.php/index/Brand/lst','index/Brand/lst','get',['ext'=>'html|htm']);
//分类页路由
Route::rule('category/:id','index/Category/index','get',['ext'=>'html|htm'],['id'=>'\d{1,3}']);
//商品详情优化
Route::rule('goods/:id','index/Goods/index','get',['ext'=>'html|htm'],['id'=>'\d{1,3}']);
//添加购物车 1
Route::rule('flowCart1','index/flow/flowCart1','get',['ext'=>'htm|html']);
//购物车 结算页面 2
Route::rule('flowCart2','index/flow/flowCart2','post',['ext'=>'htm|html']);
//购物车 支付成功页面
Route::rule('paysuccess','index/flow/paysuccess','post',['ext'=>'htm|html']);
