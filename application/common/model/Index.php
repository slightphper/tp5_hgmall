<?php 
namespace app\index\model;
use think\Model;
use think\Db;
class Index extends Model{

	//获取首页轮播图
	public function getAlternateImgs(){
		$alterImgs = Db::name('alternate_img')->where('status','=',1)->order('sort ASC')->select();
		return $alterImgs; 
	}
}