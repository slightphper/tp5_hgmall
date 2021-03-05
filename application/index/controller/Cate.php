<?php
namespace app\index\controller;
use catetree\Catetree;
use think\Db;
class Cate extends Base
{
    public function index($id)
    {
        //获取当前分类信息
        $cates = Db::name('cate')->find($id);

    	//获取当前分类及子分类id，返回数组
    	$cateTree = new Catetree();
    	$ids = $cateTree->childrenids($id,Db::name('cate'));
    	$ids[] = $id;
    	//获取当前分类下所有文章
    	$map['cate_id'] = array('IN',$ids);
    	
        $artResCache = 'artRes_'.$id;
        if(cache($artResCache)){
            $artRes = cache($artResCache);
        }else{
            $artRes = Db::name('article')->where($map)->select();
            if($this->config['cache'] == '是'){
                cache($artResCache,$artRes,$this->config['cache_time']);
            }
        }

        
        //左侧系统帮助分类
         if(cache('helpCates')){
            $helpCates = cache('helpCates');
        }else{
            $helpCates = model('cate')->getHelpCates();
            if($this->config['cache'] == '是'){
                cache('helpCates',$helpCates,$this->config['cache_time']);
            }
        }
        //左侧普通分类
        if(cache('comCates')){
            $comCates = cache('comCates');
        }else{
            $comCates = model('cate')->getComCates();
            if($this->config['cache'] == '是'){
                cache('comCates',$comCates,$this->config['cache_time']);
            }
        }
    	$this->assign([
            'cates'    => $cates,      //当前分类
            'artRes'   => $artRes,     //当前分类子分类及所属文章信息
    		'helpCates'=> $helpCates,  //左侧系统帮助(消费者帮助分类)
            'comCates' => $comCates,   //左侧普通分类
    		]);
        return view('cate');
    }
}
