<?php
namespace app\admin\controller;
use think\Db;
class Nav extends BaseController
{
	//显示前台导航
    public function lst()
    {
        $navObj = Db::name('nav');
        if(request()->isPost()){
            $data = input('post.');
            foreach ($data['sort'] as $k => $v) {
                $navObj->where('id','=',$k)->update(['sort'=>$v]);
            }
        }
    	$navRes = $navObj->order('sort ASC')->paginate(10);
    	$this->assign('navRes',$navRes);
        return view();
    }
    //添加前台导航
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//判断网址是否有协议头
    		if($data['nav_url'] && stripos($data['nav_url'],'http://') === false){
    			$data['nav_url'] = 'http://'.$data['nav_url'];
    		}
    		$add  = Db::name('nav')->insert($data);
    		if($add)
    		{
    			$this->success('添加前台导航成功','nav/lst');
    		}else
    		{
    			$this->error('添加前台导航失败');
    		}
    		return;
    	}
        return view();
    }
    //修改前台导航
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//处理url
    		if($data['nav_url'] && stripos($data['nav_url'],'http://') === false){
    			$data['nav_url'] = 'http://'.$data['nav_url'];
    		}
    		if(Db::name('nav')->update($data) !== false){
    			$this->success('修改前台导航成功','nav/lst');
    		}else{
    			$this->error('修改前台导航失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$navs = db('nav')->find($id);
    	$this->assign('navs',$navs);
        return view();
    }
    //删除前台导航
    public function del()
    {
    	$id = input('id');
    	if(Db::name('nav')->delete($id)){
    		$this->success('删除前台导航成功！','nav/lst');
    	}else
    	{
    		$this->error('删除前台导航失败！');
    	}
        return view();
    }

   

}
