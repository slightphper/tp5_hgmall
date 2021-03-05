<?php 
namespace app\index\model;
use think\Model;
use think\Db;
use catetree\Catetree;
class Category extends Model{

	//获取顶级和二级分类
	public function getCates(){
		$cateRes = $this->where('pid','=',0)->order('sort ASC')->select();
		foreach ($cateRes as $k => $v) {
			$cateRes[$k]['children'] = $this->where(array('pid'=>$v['id']))->select();		
		}
		return $cateRes;
	}

	//通过顶级分类获取二、三级分类
	public function getSonCates($id){
		$cateRes = $this->where('pid','=',$id)->select();
		foreach ($cateRes as $k => $v) {
			$cateRes[$k]['children'] = $this->where(array('pid'=>$v['id']))->select();
		}
		return $cateRes;
	}

	//通过顶级分类获取--分类推荐
	public function getCategoryRec($id){
		$cateRec = Db::name('category_rec')->where('category_id','=',$id)->select();
		return $cateRec;
	}

	//获取当前分类的推荐品牌
	public function getBrandRec($id){
		$brand = Db::name('brand');
		$data = array();
		$brandRec = Db::name('brand_rec')->where('category_id','=',$id)->find();
		$brandsIdArr = explode(',',$brandRec['brands_id']);
		foreach ($brandsIdArr as $k => $v) {
			$data['brands'][] = $brand->field('brand_name,brand_url,brand_img')->find($v);
		}
		//推广图
		$data['promotion']['pro_img'] = $brandRec['pro_img'];
		$data['promotion']['pro_url'] = $brandRec['pro_url'];
		return $data;
	}

	//获取首页大分类推荐
	public function getRecCategorys($recPosId,$pid=0){
		$_cateRes = Db::name('rec_item')->where(array('recpos_id'=>$recPosId,'value_type'=>2))->select();
		$cateRes = array();
		foreach ($_cateRes as $k => $v) {
			$cateArr = Db::name('category')->where(array('id'=>$v['value_id'],'pid'=>$pid))->find();
			if($cateArr){
				$cateRes[] = $cateArr;
			}
		}
		return $cateRes;
	}

	//获取商品面包屑导航
	public function getPosition($category_id){
		$data = $this->field('id,cate_name,pid')->select();
		return $this->_getPosition($data,$category_id);
	}
	public function _getPosition($data,$category_id){
		static $arr = array();
		$cates = $this->field('id,cate_name,pid')->find($category_id);
		if(empty($arr)){
			$arr[] = $cates;
		}
		foreach ($data as $k => $v) {
			if($v['id'] == $cates['pid']){
				$arr[] = $v;
				$this->_getPosition($data,$v['id']);
			}
		}
		//数组反转顺序
		return array_reverse($arr);
	}

	//获取所选分类下的所有商品
	public function getCateGoodsAll($cate_id){
		$cateTree = new Catetree();
		$category_id = $cateTree->childrenids($cate_id,Db::name('category'));
		$category_id[] = $cate_id;
		//查询条件
		$where['category_id'] = array('IN',$category_id);
		$where['on_sale'] = 1;

		$cateGoods = Db::name('goods')->field('id,goods_name,og_thumb,shop_price,markte_price')->where($where)->select();
		return $cateGoods;
	}
}