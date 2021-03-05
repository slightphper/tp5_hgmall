<?php 
namespace app\index\model;
use think\Model;
class CategoryAdv extends Model{
	//获取推荐分类左侧广告位图片
	public function getCategoryAdv($id){
		$_data = $this->where('category_id','=',$id)->select();
		$data = array();
		foreach ($_data as $k => $v) {
			$data[$v['position']][] = $v;
		}
		return $data;
	}
}