<?php 
namespace app\common\model;
use think\Model;
use think\Db;
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
			$data['brands'][] = $brand->find($v);
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

}