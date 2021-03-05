<?php
namespace app\admin\controller;
use think\Controller;

class Login extends Controller{

	//后台登录页
	public function login(){
		if(request()->isPost()){
			$data = input('post.');
			$Res = model('Login')->check($data);
			return $Res;
		}

		return view();
	}

	//退出登录
	public function logout(){
		session('useradmin',null);
		$this->success('退出成功','Login/login');
	}
}