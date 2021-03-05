<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Category extends Model{

	protected $field = true;
	protected static function init(){

		Category::afterInsert(function ($categorys){
			//分类id
			$categoryId = $categorys->id;
			$categorysData = input('post.');
			//处理推荐位
			if(isset($categorysData['rec_pos'])){
				foreach ($categorysData['rec_pos'] as $k => $v) {
					Db::name('rec_item')->insert(['value_type'=>2,'value_id'=>$categoryId,'recpos_id'=>$v]);
				}
			}
			
		});

		Category::beforeUpdate(function ($categorys){
			$categoryId = $categorys->id;
			$categorysData = input('post.');
			//修改推荐位
			Db::name('rec_item')->where(array('value_type'=>2,'value_id'=>$categoryId))->delete();
			if(isset($categorysData['rec_pos'])){
				foreach ($categorysData['rec_pos'] as $k => $v) {
					Db::name('rec_item')->insert(['value_type'=>2,'value_id'=>$categoryId,'recpos_id'=>$v]);
				}
			}
		});

	}
}