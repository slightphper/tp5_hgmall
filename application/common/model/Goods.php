<?php
namespace app\common\model;
use think\Model;
use catetree\Catetree;
use think\Db;
class Goods extends Model{

	//获取推荐项商品
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
	    	$categoryRec = Db::name('goods')->where($map)->order('id desc')->limit(6)->select();
	    	return $categoryRec;
	}

}