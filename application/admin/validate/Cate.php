<?php
namespace app\admin\validate;
use think\Validate;
class Cate extends Validate{

	//验证规则
	protected $rule = [
		'cate_name' => 'require|unique:cate',

	];

	protected $message = [
		'cate_name.require' => '分类名称必须填写',
		'cate_name.unique' => '分类名称不能重复',

	];
}