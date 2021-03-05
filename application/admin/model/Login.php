<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Login extends Model{

	//验证后台登录
	public function check($data){
		$username = $data['username'];
		$password = $data['password'];
		$user = Db::name('user_admin')->where('username',$username)->find();
		if($user){
			if($user['password'] == $password){
				session('useradmin',$username);
				return 0;  //账号密码正确
			}else{
				return 2; //密码不正确
			}
		}else{
			return 1; //账号不正确 
		}

	}

}