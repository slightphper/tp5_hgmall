<?php
namespace app\index\model;
use think\Model;
use catetree\Catetree;
use think\Db;
class Goods extends Model{

	//获取首页推荐项商品
	public function getRecPosGoods($recposId,$limit = ''){
		$_goodsIds = Db::name('rec_item')->where(array('value_type'=>1,'recpos_id'=>$recposId))->select();
		$goodsIds = array();
		foreach ($_goodsIds as $k => $v) {
			$goodsIds[] = $v['value_id'];
		}
		$map['id'] = array('IN',$goodsIds);
		$recGoodsRes = Db::name('goods')->field('id,goods_name,og_thumb,shop_price,markte_price')->where($map)->limit($limit)->select();
		return $recGoodsRes;
	}

	//获取首页推荐分类一二级分类下的所有的推荐商品
	public function getIndexRecposGoods($cateId,$recPosId){
			//该分类下全部子分类
	    	$cateTree = new Catetree();
	    	$cateSonId = $cateTree->childrenids($cateId,Db::name('category'));
	    	$cateSonId[] = $cateId;
	    	//推荐位表里查询符合条件的商品信息
	    	$_recPosGoods = Db::name('rec_item')->where(array('value_type'=>1,'recpos_id'=>$recPosId))->select();
	    	$recPosGoods = array();
	    	foreach ($_recPosGoods as $kk => $vv) {
	    		$recPosGoods[] = $vv['value_id'];
	    	}
	    	$map['category_id'] = array('IN',$cateSonId);
	    	$map['id'] = array('IN',$recPosGoods);
	    	$map['on_sale'] = 1;
	    	$categoryRec = Db::name('goods')->where($map)->order('id desc')->limit(6)->select();
	    	return $categoryRec;
	}

	//获取商品会员价格   逻辑:先取到session中会员id和比率，去member_price查询是否有该商品对应会员价格，
							//如果没有，用 （比率 * 商品价格） 计算价格
	public function getMemberPrice($goods_id){
		$levelId = session('member_id');
		$levelRate = session('member_rate');
		$goodsInfo = $this->find($goods_id);
		if($levelRate){
			$memberPrice = Db::name('member_price')->where(array('goods_id'=>$goods_id,'mlevel_id'=>$levelId))->find();
			if($memberPrice){
				$price = $memberPrice['mprice'];
			}else{
				$levelRate = $levelRate/100;
				$price = $goodsInfo['shop_price'] * $levelRate;
			}
		}else{
			$price = $goodsInfo['shop_price'];
		}
		return $price;
	}

}