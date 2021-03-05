<?php
namespace app\index\controller;
use think\Db;
class Article extends Base
{
    public function index($id)
    {
    	//当前文章内容
        $artsName = 'arts_'.$id; 
        if(cache($artsName)){
            $arts = cache($artsName);
        }else{
            $arts = Db::name('article')->find($id);
            if($this->config['cache'] == '是'){
                cache($artsName,$arts,$this->config['cache_time']);
            }
        }

        //获取面包屑导航
        $position = model('cate')->position($arts['cate_id']);
        // halt($position);
    	
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
            'arts'     => $arts,       //当前文章信息
    		'position' => $position,   //面包屑
    		'helpCates'=> $helpCates, 
            'comCates' => $comCates,    
    		]);
        // halt($arts);
        return view('article');
    }
}
