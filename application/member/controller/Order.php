<?php 
namespace app\member\controller;
use app\index\controller\Base;
use think\Db;
class Order extends Base{
	public $config; //声明公共配置，别的控制器也可以使用

	//显示用户中心首页
	public function orderlist()
	{
		// halt($configs);
		return view();
	}

 
	public function orderDetail()
	{
		$orderId = input('id');
		return view();
	}
}