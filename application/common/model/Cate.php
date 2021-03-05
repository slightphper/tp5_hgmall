<?php 
namespace app\common\model;
use think\Model;
class Cate extends Model{

	//获取普通分类
	public function getComCates(){
		$comCates = $this->where(array('cate_type'=>5,'pid'=>0))->select();
		//判断二级分类
		foreach ($comCates as $k => $v) {
			$comCates[$k]['children'] = $this->where(array('pid'=>$v['id']))->select();
		}
		return $comCates;
	}

	//网店帮助分类
	public function getHelpCates(){
		$helpCates = $this->where(array('cate_type'=>3,'pid'=>2))->select();
		return $helpCates;
	}

	//面包屑导航
	public function position($cateId){
		$data = $this->field('id,pid,cate_name')->select();
		return $this->_position($data,$cateId);
	}

	private function _position($data,$cateId){
		static $arr = array();
		$cates = $this->field('id,pid,cate_name')->find($cateId);
		if(empty($arr)){
			$arr[] = $cates;
		}
		foreach ($data as $k => $v) {
			if($v['id'] == $cates['pid']){
				$arr[] = $v;
				$this->_position($data,$v['id']);
			}
		}
		return array_reverse($arr);
	}

}