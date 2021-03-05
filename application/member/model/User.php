<?php
namespace app\member\model;
use think\Model;
use think\Db;
class User extends Model{

	//用户注册
	public function register($data){
		$userData = array();
		$userData['username'] = trim($data['username']);
		$userData['password'] = md5($data['password']);
		$userData['email']	  = $data['email'];
		$userData['mobile_phone'] = $data['mobile_phone'];
		$userData['register_type']= $data['register_type'];
		$userData['register_time']= time();
		$add = Db::name('user')->insert($userData);
		return $add;
	}

	//登录
	public function login($data,$backAct = "#"){
		$userData = array();
		$userData['username'] = trim($data['username']);
		$userData['password'] = md5($data['password']);
		//验证用户名或手机号或邮箱
		$users = Db::name('user')
		        ->where(array('username'=>$userData['username']))
                ->whereOr(array('email'=>$userData['username']))
                ->whereOr(array('mobile_phone'=>$userData['username']))
                ->find();
		if($users){
			if($userData['password'] == $users['password']){
				session('uid',$users['id']);
				session('username',$users['username']);
				//写入会员等级及折扣率
				$points = $users['points'];
				$memberLevel = Db::name('member_level')->where('bom_point','<=',$points)->where('top_point','>=',$points)->find();
				session('member_id',$memberLevel['id']);
				session('member_rate',$memberLevel['rate']);
				//写入cookie  *目的：保存登录信息，下次直接登录，注意：cookie的作用域(根路径)
				if(isset($data['remember'])){
					$time = 1*24*60*60;
					$username = encryption($users['username'],0);
					$password = encryption($data['password'],0);
 					cookie('username',$username,$time,'/');
					cookie('password',$password,$time,'/');
				}
				$arr = ["error" => 0,'url' => $backAct];  
				return json($arr); //user.js /419 ajax验证
			}else{
				$arr = [
					"error" => 1,
					"message" => "<i class='iconfont icon-minus-sign'>用户名或密码错误</i>",
				];
				return json($arr);
			}
		}else{
			$arr = ["error"=>1,
			'message'=>"<i class='iconfont icon-minus-sign'>用户名或密码错误</i>",
			];
			return json($arr);
		}
	}

	//退出登录
	public function logout(){
		session('uid',null); //清空session
		session('username',null); //清空session
		session('member_id',null); //清空session
		session('member_rate',null); //清空session
		
		cookie('username',null);  //清除登录cookie
		cookie('password',null);  //清除登录cookie
		return 1;
	}


}