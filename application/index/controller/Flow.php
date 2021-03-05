<?php
namespace app\index\controller;
use think\Db;
class Flow extends Base{

	//添加购物车
	public function addToCart(){
		// 最终格式->   '1-3,4 => 1'
		$data = input('post.');

		$goodsObj  = json_decode($data['goods']);  //ajax返回解码后是 对象
		$goodsId   = $goodsObj->goods_id; 		   //["goods_id"] =&gt; int(12)
		$goodsAttr = $goodsObj->goods_attr;		   //["goods_attr"] =&gt; string(7) "130,127"
		$goodsNum  = $goodsObj->number;			   //["number"] =&gt; string(1) "1"
		model('Cart')->addToCart($goodsId,$goodsAttr,$goodsNum);
		// dump($data);die();
		//error = 0  表示有库存
		$result = ['error' => 0,'one_step_buy' => 1];
		return json($result);
		
	}

	//显示购物车页面1（展示商品）
	public function flowCart1(){
		$getCartGoods = model('Cart')->getCartGoodsList();
		// dump($getCartGoods);die();
		$this->assign([
			'getCartGoods' => $getCartGoods,
			]);
		return view('flowcart1');
	}

	//计算购物车内容及实时改动价格
	public function cartTotal(){ 
		$goodsIds = input('goodsIds');
		$result = model('Cart')->cartTotal($goodsIds);
		return json($result);
	}

	//点击删除购物车商品
	public function delCart(){
		$idAttr = input('id_attr');
		model('Cart')->delCart($idAttr);
		$this->redirect('index/Flow/flowCart1',302);
	}
	//复选框勾选 批量 删除购物车商品（异步，flowcart1.html）
	public function delCarts(){
		$cart_ids = input('cart_value');  //接收传来的值  12-130,127@12-129,126
		$result = model('Cart')->delCarts($cart_ids);
		//  1：购物车不为空(转跳购物车)  0：购物车为空(跳购物车为空的页面)
		if($result == 1){  
			return 1;
		}else{
			return 0;
		}
	}

	//修改购物车商品数量  flowcart1.htm 异步
	public function updateCart(){
		$idAttr = input('rec_id');
		$goodsNum = input('goods_number');
		$result = model('Cart')->updateCart($idAttr,$goodsNum);
		return $result;
	}

	//获取购物车商品数量(   <商品详情页>  添加购物车按钮)
	//实时在头部购物车导航显示数量即可   
	public function cartGoodsNum(){
		$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
		$cartGoodsNum = 0;
		foreach ($cart as $k => $v) {
		 	$cartGoodsNum += $v;
		 } 
		 return json(['cartGoodsNum' => $cartGoodsNum]);
	}

	//弹出登录框
	public function loginDialog(){			
		$backUrl = input('back_act','');    //登录成功后转跳的地址
		$ajax_login_url = url('member/Account/login');
		$register= url('member/Account/reg');
		$getPassword = url('member/Account/getPassword');
		$content = "<div class=\"login-wrap\">\n    \n    <div class=\"login-form\">\n    \t    \t<div class=\"coagent\">\n            <div class=\"tit\"><h3>用第三方账号直接登录<\/h3><span><\/span><\/div>\n            <div class=\"coagent-warp\">\n            \t                                    <a href=\"#\" class=\"qq\"><b class=\"third-party-icon qq-icon\"><\/b><\/a>\n                                            <\/div>\n        <\/div>\n                <div class=\"login-box\">\n            <div class=\"tit\"><h3>账号登录<\/h3><span><\/span><\/div>\n            <div class=\"msg-wrap\"><\/div>\n            <div class=\"form\">\n            \t<form name=\"formLogin\" action=\"user.php\" method=\"post\" onSubmit=\"userLogin();return false;\">\n                \t<div class=\"item\">\n                        <div class=\"item-info\">\n                            <i class=\"iconfont icon-name\"><\/i>\n                            <input type=\"text\" id=\"loginname\" name=\"username\" class=\"text\" value=\"\" placeholder=\"用户名\/邮箱\/手机\" \/>\n                        <\/div>\n                    <\/div>\n                    <div class=\"item\">\n                        <div class=\"item-info\">\n                            <i class=\"iconfont icon-password\"><\/i>\n                            <input type=\"password\" style=\"display:none\"\/>\n                            <input type=\"password\" id=\"nloginpwd\" name=\"password\" value=\"\" class=\"text\" placeholder=\"密码\" \/>\n                        <\/div>\n                    <\/div>\n                                        <div class=\"item\">\n                        <input id=\"remember\" name=\"remember\" type=\"checkbox\" class=\"ui-checkbox\">\n                        <label for=\"remember\" class=\"ui-label\">请保存我这次的登录信息。<\/label>\n                    <\/div>\n                    <div class=\"item item-button\">\n                    \t<input type=\"hidden\" name=\"dsc_token\" value=\"88feda8561dc803c2ab8237c3b465fe4\" \/>\n                        <input type=\"hidden\" name=\"act\" value=\"act_login\" \/>\n                        <input type=\"hidden\" name=\"back_act\" value=\"".$backUrl."\" \/>\n                        <input type=\"submit\" name=\"submit\" value=\"登&nbsp;&nbsp;录\" class=\"btn sc-redBg-btn\" \/>\n                    <\/div>\n                    <div class=\"lie\">\n                    \t<a href=\"".$getPassword."\" class=\"notpwd gary fl\" target=\"_blank\">忘记密码？<\/a>\n                    \t<a href=\"".$register."\" class=\"notpwd red fr\" target=\"_blank\">免费注册<\/a>                    <\/div>\n                <\/form>\n            <\/div>\n    \t<\/div>        \n    <\/div>\n    <script type=\"text\/javascript\">\n\t\tvar username_empty=\"<i><\/i>请输入用户名\";\n    \tvar username_shorter=\"<i><\/i>用户名长度不能少于 4 个字符。\";\n    \tvar username_invalid=\"<i><\/i>用户名只能是由字母数字以及下划线组成。\";\n    \tvar password_empty=\"<i><\/i>请输入密码\";\n    \tvar password_shorter=\"<i><\/i>登录密码不能少于 6 个字符。\";\n    \tvar confirm_password_invalid=\"<i><\/i>两次输入密码不一致\";\n    \tvar captcha_empty=\"<i><\/i>请输入验证码\";\n    \tvar email_empty=\"<i><\/i>Email 为空\";\n    \tvar email_invalid=\"<i><\/i>Email 不是合法的地址\";\n    \tvar agreement=\"<i><\/i>您没有接受协议\";\n    \tvar msn_invalid=\"<i><\/i>msn地址不是一个有效的邮件地址\";\n    \tvar qq_invalid=\"<i><\/i>QQ号码不是一个有效的号码\";\n    \tvar home_phone_invalid=\"<i><\/i>家庭电话不是一个有效号码\";\n    \tvar office_phone_invalid=\"<i><\/i>办公电话不是一个有效号码\";\n    \tvar mobile_phone_invalid=\"<i><\/i>手机号码不是一个有效号码\";\n    \tvar msg_un_blank=\"<i><\/i>用户名不能为空\";\n    \tvar msg_un_length=\"<i><\/i>用户名最长不得超过15个字符，一个汉字等于2个字符\";\n    \tvar msg_un_format=\"<i><\/i>用户名含有非法字符\";\n    \tvar msg_un_registered=\"<i><\/i>用户名已经存在,请重新输入\";\n    \tvar msg_can_rg=\"<i><\/i>可以注册\";\n    \tvar msg_email_blank=\"<i><\/i>邮件地址不能为空\";\n    \tvar msg_email_registered=\"<i><\/i>邮箱已存在,请重新输入\";\n    \tvar msg_email_format=\"<i><\/i>格式错误，请输入正确的邮箱地址\";\n    \tvar msg_blank=\"<i><\/i>不能为空\";\n    \tvar no_select_question=\"<i><\/i>您没有完成密码提示问题的操作\";\n    \tvar passwd_balnk=\"<i><\/i>密码中不能包含空格\";\n    \tvar msg_phone_blank=\"<i><\/i>手机号码不能为空\";\n    \tvar msg_phone_registered=\"<i><\/i>手机已存在,请重新输入\";\n    \tvar msg_phone_invalid=\"<i><\/i>无效的手机号码\";\n    \tvar msg_phone_not_correct=\"<i><\/i>手机号码不正确，请重新输入\";\n    \tvar msg_mobile_code_blank=\"<i><\/i>手机验证码不能为空\";\n    \tvar msg_mobile_code_not_correct=\"<i><\/i>手机验证码不正确\";\n    \tvar msg_confirm_pwd_blank=\"<i><\/i>确认密码不能为空\";\n    \tvar msg_identifying_code=\"<i><\/i>验证码不能为空\";\n    \tvar msg_identifying_not_correct=\"<i><\/i>验证码不正确\";\n    \t\t\/* *\n\t\t * 会员登录\n\t\t*\/ \n\t\tfunction userLogin()\n\t\t{\n\t\t\tvar frm = $(\"form[name='formLogin']\");\n\t\t\tvar username = frm.find(\"input[name='username']\");\n\t\t\tvar password = frm.find(\"input[name='password']\");\n\t\t\tvar captcha = frm.find(\"input[name='captcha']\");\n\t\t\tvar dsc_token = frm.find(\"input[name='dsc_token']\");\n\t\t\tvar error = frm.find(\".msg-error\");\n\t\t\tvar msg = '';\n\t\t\t\n\t\t\tif(username.val()==\"\"){\n\t\t\t\terror.show();\n\t\t\t\tusername.parents(\".item\").addClass(\"item-error\");\n\t\t\t\tmsg += username_empty;\n\t\t\t\tshowMesInfo(msg);\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\t\n\t\t\tif(password.val()==\"\"){\n\t\t\t\terror.show();\n\t\t\t\tpassword.parents(\".item\").addClass(\"item-error\");\n\t\t\t\tmsg += password_empty;\n\t\t\t\tshowMesInfo(msg);\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\t\n\t\t\tif(captcha.val()==\"\"){\n\t\t\t\terror.show();\n\t\t\t\tcaptcha.parents(\".item\").addClass(\"item-error\");\n\t\t\t\tmsg += captcha_empty;\n\t\t\t\tshowMesInfo(msg);\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\tvar back_act=frm.find(\"input[name='back_act']\").val();\n\t\t\t\n\t\t\t\t\t\t\tAjax.call( '".$ajax_login_url."', 'username=' + username.val()+'&password='+password.val()+'&dsc_token='+dsc_token.val()+'&captcha='+captcha.val()+'&back_act='+back_act, return_login , 'POST', 'JSON');\n\t\t\t\t\t}\n\t\t\n\t\tfunction return_login(result)\n\t\t{\n\t\t\tif(result.error>0)\n\t\t\t{\n\t\t\t\tshowMesInfo(result.message);\t\n\t\t\t}\n\t\t\telse\n\t\t\t{\n\t\t\t\tif(result.ucdata){\n\t\t\t\t\t$(\"body\").append(result.ucdata)\n\t\t\t\t}\n\t\t\t\tlocation.href=result.url;\n\t\t\t}\n\t\t}\n\t\t\n\t\tfunction showMesInfo(msg) {\n\t\t\t$('.login-wrap .msg-wrap').empty();\n\t\t\tvar info = '<div class=\"msg-error\"><b><\/b>' + msg + '<\/div>';\n\t\t\t$('.login-wrap .msg-wrap').append(info);\n\t\t}\n\t<\/script>\n<\/div>\n";
		$content=stripcslashes($content);  //反转义字符串
		//dump($content);die();
		return json(['error'=>0,'message'=>'','content'=>$content]);
	} 

	//显示购物车2 订单结算页
	public function flowCart2(){
		//获取所选属性及拆分数组
		$attrStr = input('cart_value');
		$attr_id = explode('@',$attrStr);
		// dump($attr_id);
		//遍历购物车内容并判断
		$cartList = model('Cart')->getCartGoodsList();
		// dump($cartList);
		$checkGoods = array();
		foreach ($cartList as $k => $v) {
			if(in_array($k,$attr_id)){
				$checkGoods[$k] = $v; 
			}
		}
		// dump($checkGoods);   //打印订单商品
		//获取当前用户的收货地址
		$uid = session('uid');
		$userAddr = Db::name('user_address')->where('user_id',$uid)->find();
		//计算订单内商品总数及总价格
		$goods_total = model('Cart')->cartTotal($attrStr);
		// dump($goods_total);
		$this->assign([
			'checkGoods' => $checkGoods, //订单所选商品信息
			'goods_total'=> $goods_total,//订单总计			
			'userAddr'	 => $userAddr,   //用户收货地址
			'attrStr'	 => $attrStr,    //赋值到模板中,需要传给flowCart3
		]);
		return view('flowcart2');
	}

	// 3、 订单提交、写入数据（user_address / order / order_goods）  可从flowCart2打印传来的数据
	public function flowCart3(){
		// dump($_POST);die();   //打印结算页面传来的信息
		//判断用户是否登录
		$uid = session('uid');
		$attrStr = input('cart_value');  //接收订单商品  格式:string(21) "12-130,127@12-128,126"

		//接收用户收货信息   user_address
		$addrData['user_id']	  = $uid;
		$addrData['name']		  = input('name');
		$addrData['mobile_phone'] = input('mobile_phone');
		$addrData['province'] 	  = input('province');
		$addrData['city'] 		  = input('city');
		$addrData['district']     = input('district');
		$addrData['address'] 	  = input('address');
		$addrData['zipcode']	  = input('zipcode');
		//处理用户收货信息  如第一次下单，address表插入一条信息，否则 修改信息(用户只存一个地址)
		$userAddr = Db::name('user_address')->where('user_id',$uid)->find();
		if($userAddr){
			Db::name('user_address')->where('user_id',$uid)->update($addrData);
		}else{
			Db::name('user_address')->insert($addrData);
		}
		//处理订单表 信息  order
		//接收之前先处理订单商品总金额
		$order_total_price = model('Cart')->orderGoodsPrice($attrStr);
		// dump($order_total_price);die();
		$orderData['out_trade_no'] = time().rand(100000,999999); 
		$orderData['user_id']      = $uid; 
		$orderData['goods_total_price'] = $order_total_price;   //订单商品总金额
		$orderData['post_spent']   = 0;   //运费接口  未完成  写死 0元
		$orderData['order_total_price'] = ($orderData['goods_total_price'] + $orderData['post_spent']); 
		$orderData['payment'] 	   = input('payment'); 
		$orderData['distribution'] = input('distribution'); 
		$orderData['name']		   = input('name');
		$orderData['mobile_phone'] = input('mobile_phone');
		$orderData['province'] 	   = input('province');
		$orderData['city'] 		   = input('city');
		$orderData['district']     = input('district');
		$orderData['address'] 	   = input('address');
		$orderData['order_time']   = time();
		//插入order表后返回新增数据的主键
		$orderId = Db::name('order')->insertGetId($orderData);
		//处理订单--商品表 信息
		if($orderId){    //判断是否回传新增订单主键
			//变为数组形式,		$attrStr在方法开始时接收
			$attr_id = explode('@',$attrStr);
			//遍历购物车内容并判断  
			$cartList = model('Cart')->getCartGoodsList();
			$orderGoods = array();
			foreach ($cartList as $k => $v) {
				if(in_array($k,$attr_id)){
					$orderGoods[$k] = $v; 
				}
			}
			//$orderGoods数组为 订单商品  遍历后一个商品存入一条数据
			foreach ($orderGoods as $k1 => $v1) {
				$goodsData['goods_id'] 		 = $v1['id'];
				$goodsData['goods_name'] 	 = $v1['goods_name'];
				$goodsData['member_price']   = $v1['member_price'];
				$goodsData['goods_attr_id']  = $k1; 	//键值，内含商品id
				$goodsData['goods_attr_str'] = $v1['goods_attrStr'];
				$goodsData['goods_num'] 	 = $v1['goods_num'];
				$goodsData['total_price']	 = $v1['member_price'] * $v1['goods_num'];
				$goodsData['order_id']   	 = $orderId;
				Db::name('order_goods')->insert($goodsData);
			}
			$this->success('下单成功',url('index/flow/flowCart4',array('orderId'=>$orderId),''));
		}
	}

	//4、成功提交订单 并显示支付方式(接入支付宝接口)
	public function flowCart4(){
		$orderId = input('orderId');
		$orderInfo = Db::name('order')->find($orderId);
		//接入支付宝接口  并赋值到页面按钮
		if($orderInfo['payment'] == 1 && $orderInfo['pay_status'] == 0){
			include(PAY_URL.'/pay/alipay/alipayapi.php');
			$payBtn = $html_text;
			$this->assign('payBtn',$payBtn);
		}

		$this->assign([
			'orderInfo' => $orderInfo,
			]);
		return view('flowcart4');
	}

	//微信支付接口  生成二维码
	public function wxewm($outTradeNo){
		//根据订单号查询支付金额
		$orderTotalPrice = Db::name('order')->where('out_trade_no',$outTradeNo)->value('order_total_price');
		$orderTotalPrice = $orderTotalPrice * 100; //换算成 分
		//引入接口文件，wxpay/index2.php
		$payPlus = PAY_URL.'/pay/wxpay/';
		include($payPlus.'index2.php');
		$obj = new \WeiXinPay2();
		$qrurl = $obj->getQrUrl($outTradeNo,$orderTotalPrice);
		 //2.生成二维码
		\QRcode::png($qrurl);
	}

	//微信付款成功后 回调请求
	public function wxPaySuccess(){
		$payPlus = PAY_URL.'/pay/wxpay/';
		include($payPlus.'notify.php');
		new \Notify();
	}

	//ajax验证微信支付 是否支付成功
	public function getPayStatus(){
		$out_trade_no = input('out_trade_no');
		$payStatus = Db::name('order')->where('out_trade_no',$out_trade_no)->value('pay_status');
		return json(['payStatus'=>$payStatus]);
	}

	//5、付款成功 跳转付款成功页面
	public function paySuccess(){
		$orderInfo['name'] = input('order_name');
		$orderInfo['order_total_price'] = input('order_price');
		$orderInfo['phone'] = input('order_phone');
		$orderInfo['addr'] = input('order_addr');
		$this->assign('orderInfo',$orderInfo);
		return view('paysuccess');
	}
	

}
	
	/*计算购物车总结:开始从商品详情页面加入购物车，购物车获取数据结构为：'1-3,4 -> 1',
	 * 可以读取数据，但是复选框修改时出问题，ajax实时计算购物车时点选的是传来的商品id，同一商品如果有不同
	 * 同属性的话购物车会分为开列出，导致分开列出商品传来的id一样，这样就无法判断复选框状态。
	 * 解决办法：读取购物车数据，键值为'1-2,3'，从这里入手，把复选框值改为键值，这样就获取跟数据存储键值
	 * 对应，紧接着又有问题,获取复选id变为：'1-2,3,2-4,5' 这种格式怎么拼装？
	 * 解决办法：把连接符号改为其他"@",变为：'1-2,3@2-4,5' 在拆分成数组,在做具体逻辑，具体逻辑看
	 * Model(Cart)控制器里面cartTotal方法。
	 */

	/*
	添加购物车问题描述：在点选属性添加购物车时，第一次选中属性添加成功(无问题),
	再返回商品详情页面时，再次选购时，如果换选属性添加商品,就会出现属性提交不成功的问题，
	页面无报错，跳转购物车时属性出错，或没有属性，导致页面排版错误？

	解决： 检查发现，在页面goods.html中的js代码  //单选属性的选中及其兄弟元素的去除选中
	这段代码后有，return false导致， 去除后发现问题解决.

	同时又发现新问题，添加同一商品不同属性时，全部添加一遍后，继续添加叠加商品数量时，商品数量不能正确的添加到所选属性后面(一直添加到最后添加成功的商品后面)，
	
	解决： 添加购物车时重新开启一个窗口，在common.js 修改成功跳转
		 window.open(ajax_cart_url,"win1");  // win1 参数-》页面只存在一个
 	*/