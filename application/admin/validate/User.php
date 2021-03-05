<?php
namespace app\admin\validate;
use think\Validate;
class User extends Validate{

	protected $rule = [
		'username' => 'require|unique:user',
		'password' => 'require|min:6',
		'mobile_phone' => 'unique:user',
		// 'points' => 'num',
	];

	protected $message = [
		'username.require' => '请填写用户名',
		'username.unique'  => '已存在用户名，请重新填写！',
		'password.require' => '请填写密码',
		'password.min'		   => '密码不能少于6位',
		'mobile_phone'	   => '手机号不能重复',
		// 'points'		   => '积分必须为数字',
	];
}