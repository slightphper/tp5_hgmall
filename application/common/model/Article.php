<?php 
namespace app\common\model;
use think\Model;
class Article extends Model{

	public function getFooterArts()
	{
		//获取底部帮助分类
		$helpCateRes = model('cate')->where(array('cate_type'=>3))->order('sort asc')->select();
		foreach ($helpCateRes as $k => $v) {
			$helpCateRes[$k]['arts'] = $this->where(array('cate_id'=>$v['id']))->select();
		}
		return $helpCateRes;
	}

	//获取底部网站信息
	public function getShopInfo(){
		$artArr = $this->where('cate_id','=',3)->field('id,title')->select();
		return $artArr;
	}

	//获取首页公告、促销 文章
	public function getMainArts($id,$limit){
		$arts = $this->where('cate_id','=',$id)->order('id DESC')->limit($limit)->select();
		return $arts;
	}
}