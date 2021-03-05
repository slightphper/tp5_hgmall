<?php
namespace app\index\model;
use think\Db;
use think\Model;
class Cart extends Model{

	/* 添加购物车
	 * 逻辑： $goodsArr = ['1-3,4' => 4 , '1-2,4' => 1];
	 * 				'商品id-商品所选属性id'=> 数量
	 * 	目标数组->序列化->存入cookie->反序列化->取出数组
	 */
	public function addToCart($goodsId,$goodsAttr = '',$goodsNum = 1){
		$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
		$key = $goodsId.'-'.$goodsAttr;
		//逻辑过程：
		//判断购物车是否已经有该商品，如果有直接加数量即可
		if(isset($cart[$key])){
			$cart[$key] = $cart[$key]+$goodsNum;
		}else{
			$cart[$key] = $goodsNum;
		}
		$time = time()+30*24*60*60;
		setcookie('cart',serialize($cart),$time,'/');
	}

	//清空购物车
	public function clearCart(){
		setcookie('cart','',1,'/');
	}

	//删除一条购物车记录
	public function delCart($idAttr){
		$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
		$key = $idAttr;
		unset($cart[$key]);
		$time = time()+30*24*60*60;
		setcookie('cart',serialize($cart),$time,'/');
	}

	//批量删除购物车记录
	public function delCarts($cart_ids){
		$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array(); 
		$cart_ids = explode('@',$cart_ids);  //获取的字符串拆分成数组
		foreach ($cart_ids as $k => $v) {
			unset($cart[$v]);
		}
		if(!empty($cart)){
			$time = time()+30*24*60*60;
			setcookie('cart',serialize($cart),$time,'/');
			return 1;
		}else{
			$time = time()+30*24*60*60;
			setcookie('cart',serialize($cart),$time,'/');
			return 0;
		}
	}

	//修改购物车商品数量
	public function updateCart($idAttr,$goodsNum = 1){
		$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
		$key = $idAttr;
		$cart[$key] =  $goodsNum;
		$time = time()+30*24*60*60;
		setcookie('cart',serialize($cart),$time,'/');
		return json(['error'=> 0]);
	}


	//读取cookie，获得购物车商品
	public function getCartGoodsList(){
		$goods = model('Goods');
		$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
		$_cart = array();
		foreach ($cart as $k => $v) {
			$arr = explode('-',$k); //拆分，$arr[0] 为商品id，如果有属性 为 $arr[1] 
			//获取商品基本信息
			$goodsInfo = $goods->field('id,goods_name,mid_thumb,shop_price')->find($arr[0]);
			//获取商品会员价格
			$memberPrice = $goods->getMemberPrice($arr[0]);
			//拼成新数组
			$_cart[$k]['id']		   = $goodsInfo['id'];
			$_cart[$k]['goods_name']   = $goodsInfo['goods_name'];
			$_cart[$k]['mid_thumb']    = $goodsInfo['mid_thumb'];
			$_cart[$k]['shop_price']    = $goodsInfo['shop_price'];
			$_cart[$k]['member_price'] = $memberPrice;
			$_cart[$k]['goods_num']    = $v;
			$_cart[$k]['goods_attrStr']= ''; //事先声明商品属性，避免商品无属性调用出错
			//联表查询商品属性值, 条件：该商品有属性值
			if($arr[1]){
				/* 属性名  属性值  价格
				 * 例:颜色	XXL   10.00    
				 *  	遍历后预想样式： 尺码:XXL(￥20元)   
				 */ 
				$goodsAttrStr = array();
				$goodsAttrPrice = 0;
				$goodsAttrRes = Db::name('goods_attr')->alias('ga')->field('ga.*,a.attr_name')->join('attr a','ga.attr_id = a.id')->where('ga.id','in',$arr[1])->select();
				foreach ($goodsAttrRes as $k1 => $v1) {
					$goodsAttrStr[] = $v1['attr_name'].'：'.$v1['attr_value'].'(￥'.$v1['attr_price'].'元)';
					$goodsAttrPrice += $v1['attr_price'];
					
				}
				$goodsAttrStr = implode('<br />', $goodsAttrStr);
				$_cart[$k]['goods_attrStr'] = $goodsAttrStr;     //拼装后商品属性格式
				$_cart[$k]['member_price'] += $goodsAttrPrice;   //计算属性价格后 会员价格
				$_cart[$k]['shop_price'] += $goodsAttrPrice;    //计算属性价格后 商品原价
			}
			$_cart[$k]['subtotal'] = $_cart[$k]['member_price'] * $v;
		}
		// halt($_cart);
		return $_cart;
	}


	//计算购物车内容及实时改动价格
	public function cartTotal($goodsIds){
		$goodsIds = explode('@',$goodsIds);  //商品id-属性  '12-52,46' @ '11-21,20'  
		$arr['goods_amount'] = 0;		//商品总价
		$arr['subtotal_number'] = 0;	//商品总数
		$arr['save_total_amount'] = 0;	//总节省钱数
		//获取购物车所有商品信息
		$getCartGoods = model('Cart')->getCartGoodsList();
		foreach ($getCartGoods as $k => $v) {
			foreach ($goodsIds as $k1 => $v1) {
				if($v1 === $k){
					$arr['goods_amount'] += $v['member_price'] * $v['goods_num'];
					$arr['save_total_amount'] += ($v['shop_price'] - $v['member_price'])*$v['goods_num'];
					$arr['subtotal_number'] += $v['goods_num'];
				}
			}
			
		}   
		return $arr;
	}

	//计算结算页 下单商品总金额
	public function orderGoodsPrice($cart_value){
		$attr_ids = explode('@',$cart_value); //拆分为数组
		//获取购物车全部商品，并定义新数组
		$cart = $this->getCartGoodsList();
		$order_list = array();
		//遍历筛选订单商品  
		foreach ($cart as $k => $v) {
			if(in_array($k, $attr_ids)){
				$order_list[$k] = $v;
			}
		}
		//遍历订单  计算订单商品总金额
		$arr['goods_total_price'] = 0;
		foreach ($order_list as $k1 => $v1) {
			$arr['goods_total_price'] += $v1['member_price'] * $v1['goods_num'];
		}
		return $arr['goods_total_price'];

	}


}