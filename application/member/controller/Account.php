<?php
namespace app\member\controller;
use app\index\controller\Base;
use think\Db;
use ChuanglanSmsHelper\ChuanglanSmsApi;
use PHPMailer\PHPMailer\PHPMailer;
class Account extends Base{
	//注册
	public function reg(){
		if(request()->isPost()){
			$data = input('post.');
			$add = model('User')->register($data);
			if($add){
				$this->login(); //注册成功后先登录系统
				$this->success('注册成功！','index/Index/index');
			}else{
				$this->success('注册失败！');
			}
		}
		return view();
	}
	//登录
	public function login(){
		if(request()->isPost()){
			$data = input('post.');
			$backAct = input('back_act','');
			$loginer = model('User')->login($data,$backAct);
			return $loginer;
		}
		return view();
	}

	//验证用户名
	public function checkUsername(){
		$username = input('username');
		$userInfo = Db::name('user')->where('username','=',$username)->find();
		if($userInfo){
			return false;
		}else{
			return true;
		}
	}

	//验证手机号
	public function checkPhone(){
		$mobile_phone = input('mobile_phone');
		$mPhone = Db::name('user')->where('mobile_phone','=',$mobile_phone)->find();
		if($mPhone){
			return false;
		}else{
			return true;
		}
	}
	//验证手机验证码
	public function checkMobileCode(){
		$phoneCode = input('mobile_code');
		// return true;  //测试用，后期删除
		if($phoneCode == session('mobileCode')){
			return true;
		}else{
			return false;
		}
	}

	//验证邮箱
	public function checkEmail(){
		$email = input('email');
		$emailInfo = Db::name('user')->where('email','=',$email)->find();
		// return true;  //测试用，后期删除
		if($emailInfo){
			return false;
		}else{
			return true;
		}
	}
	//验证邮箱验证码
	public function checkEamilCode(){
		$emailCode = input('send_code');
		// return true;  //测试用，后期删除
		if($emailCode == session('emailCode')){
			return true;
		}else{
			return false;
		}
	}

	//判断用户是否登录
	public function checkLogin(){
		$uid = session('uid');
		if($uid){
			$arr['error'] 	 = 0;
			$arr['uid'] 	 = $uid;
			$arr['username'] = session('username');
			return json($arr);
		}else{
			//判断浏览器中是否有cookie存在
			if(cookie('username') && cookie('password')){
				$data['username'] = encryption(cookie('username'),1);
				$data['password'] = encryption(cookie('password'),1);
				model('user')->login($data);
					$arr['error'] 	 = 0;
					$arr['uid'] 	 = $uid;
					$arr['username'] = session('username');
					return json($arr);
			}
			
			$arr['error'] = 1;
			return json($arr);
		}
	}

    //退出登录   已改为ajax实时验证，在Account控制器里
	public function logout(){
		$logout = model('User')->logout();
		if($logout){
			$arr['error'] = 0;
			return json($arr);			
		}
		// model('User')->logout();
		// $this->success('退出成功','index/Index/index');
	}
	//找回密码
	public function getPassword(){

		return view('get_password');
	}
	//找回密码-> 验证手机号并发送信息
	public function checkSendMsg(){
		$data = input('post.');
		$phoneNum = $data['phoneNum'];
		if($phoneNum){
			$users = Db::name('User')->where('mobile_phone','=',$phoneNum)->find();
			if($users){
				$this->sendMsg(1);
				$arr['status'] = 0;
				$arr['msg'] = '';
				return json($arr);
			}else{
				$arr['status'] = 2;
				$arr['msg'] = '手机号码不存在 无法通过该号码找回密码！';
				return json($arr);
			}
		}else{
			$arr['status'] = 1;
			$arr['msg'] = '请填写手机号码！';
			return json($arr);
		}
	}

	//找回密码 验证手机验证码是否正确
	public function checkPhoneCode(){
		$data = input('post.');
		$mobileCode = trim($data['mobile_code']);
		$mobilePhone = session('getPwdPhoneNum');
		if($mobileCode == session('getPwdCode')){
			$password = mt_rand(100000,999999);
			$_password = md5($password);
			$update = Db::name('user')->where('mobile_phone','=',$mobilePhone)->update(['password'=>$_password]);
			if($update){
				$this->sendMsg(2,$password);
				$msg = '新密码已发送至手机，请及时查收！';
				return $msg;
			}else{
				$msg = '请输入正确的手机号和验证码！';
				return $msg;
			}	
		}else{
			$msg = '请输入正确的手机号和验证码！';
			return $msg;
		}
	}

	//找回密码  用户名和邮箱发送找回
	public function sendPwdEamil(){
		$data = input('post.');
		$userData['username'] = trim($data['user_name']);
		$userData['email'] = trim($data['email']);
		//验证信息
		$users = Db::name('user')->where(array('username'=>$userData['username']))->find();
		if($users){
			if($users['email'] == $userData['email']){
				$password = mt_rand(100000,999999);
				$_password = md5($password);
				$update = Db::name('user')->where('username','=',$userData['username'])->update(['password'=>$_password]);
				if($update){
					$this->sendmail($userData['email'],$password);
					$msg['status'] = 0;
					$msg['msg'] = '邮箱已发送，请及时查收！';
				}else{
					$msg['status'] = 3;
					$msg['msg'] = '邮箱和用户名不正确,请重新输入！';
				}
			}else{
				$msg['status'] = 2;
				$msg['msg'] = '邮箱和用户名不正确,请重新输入！';
			}
		}else{
			$msg['status'] = 1;
			$msg['msg'] = '用户名和邮箱不正确,请重新输入！';
		}
		$this->assign([
			'msg' => $msg,
		]);	
		return view('index@common/tip_info');
	}

	//发送短信验证  type: 0: 注册  1:手机找回密码 2:向用户发送密码
	public function sendMsg($type = 0,$password = 0){
		$clapi  = new ChuanglanSmsApi();
		$code = mt_rand(1000,9999);
		$tipMsg = '';
		if($password == 0){
			$tipMsg = '您好，您的验证码是'.$code;
		}else{
			$tipMsg = '您好，您的新密码是'.$password;
		}
		if($password == 0){
			$phoneNum = trim(input('phoneNum'));
		}else{
			$phoneNum = session('getPwdPhoneNum');
		}
		$result = $clapi->sendSMS($phoneNum,$tipMsg);
		if(!is_null(json_decode($result))){

			$output=json_decode($result,true);
			if(isset($output['code'])  && $output['code']=='0'){
				if($type == 0){
					session('mobileCode',$code);
					$msg = ['status'=>0,'msg'=>'发送成功'];  //
					return json($msg);
				}else{
					session('getPwdCode',$code);
					session('getPwdPhoneNum',$phoneNum);
					$msg = ['status'=>0,'msg'=>'发送成功'];  //
					return json($msg);
				}
			}else{
				// $msg = ['status'=>1,'msg'=>$output['errorMsg']];
				$msg = ['status'=>1,'msg'=>'发送失败'];
				return json($msg);
			}
		}else{
				$msg = ['status'=>2,'msg'=>'内部错误'];
				return json($msg);
		}
	}

	//注册验证邮箱
	public function sendmail($email = '',$password = ''){
		if($email){
			$to = $email;
		}else{
			$to = input('email');
		}
		$title = "商城验证码";
		$code = mt_rand(1000,9999);
		$content = '';
		if($password){
			$content = "您的新密码是：".$password;
		}else{
			$content = "你的验证码是：".$code;
		}
		
		$mail = new PHPMailer();
	    // 设置为要发邮件
	    $mail->IsSMTP();
	    // 是否允许发送HTML代码做为邮件的内容
	    $mail->IsHTML(TRUE);
	    $mail->CharSet='UTF-8';
	    // 是否需要身份验证
	    $mail->SMTPAuth=TRUE;
	    /*  邮件服务器上的账号是什么 -> 到163注册一个账号即可 */
	    $mail->From="345348563@qq.com";
	    $mail->FromName="张子恒";
	    $mail->Host="smtp.qq.com";  //发送邮件的服务协议地址
	    $mail->Username="345348563@qq.com";
	    $mail->Password="kmlozomxfxnrbjbc";
	    $mail->SMTPSecure = "ssl";
	    // 发邮件端口号默认25
	    $mail->Port = 465;
	    // 收件人
	    $mail->AddAddress($to);
	    // 邮件标题
	    $mail->Subject=$title;
	    // 邮件内容
	    $mail->Body=$content;
	    if($mail->Send()){
	    	//记录邮箱验证码
	    	session('emailCode',$code);
			$msg = ['status'=>0,'msg'=>'发送成功'];
			return json($msg);
		}else{
			$msg = ['status'=>1,'msg'=>'发送失败'];
			return json($msg);
		}
	}


}