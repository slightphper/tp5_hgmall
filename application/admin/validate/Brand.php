<?php
namespace app\admin\validate;
use think\Validate;
class Brand extends Validate{


	/*
	 * 验证需要做修改，不想用跳转，想要用异步请求来实现，有空要做一下，目前项目就不做验证跳转了 
	 */

	//验证规则
	protected $rules = [
		'brand_name' => 'require|unique:brand',
		'brand_url'  => 'url',
		'brand_description' => 'min:6',
	];

	protected $message = [
		'brand_name.require' => '品牌名称必须填写',
		'brand_name.unique' => '品牌名称不能重复',
		'brand_url.url' => 'url格式不正确',
		'brand_description.min' => '描述不能少于6位',
	];
}