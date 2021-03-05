<?php
namespace app\admin\controller;
use think\Db;
class MemberLevel extends BaseController
{
	//显示会员级别
    public function lst()
    {
    	$mlRes = Db::name('member_level')->order('id ASC')->paginate(10);
    	$this->assign('mlRes',$mlRes);
        return view();
    }
    //添加会员级别
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');   
    		$add  = Db::name('member_level')->insert($data);
    		if($add)
    		{
    			$this->success('添加会员级别成功','member_level/lst');
    		}else
    		{
    			$this->error('添加会员级别失败');
    		}
    		return;
    	}
        return view();
    }
    //修改会员级别
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		if(Db::name('member_level')->update($data) !== false){
    			$this->success('修改会员级别成功','member_level/lst');
    		}else{
    			$this->error('修改会员级别失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$mls = Db::name('member_level')->find($id);
    	$this->assign('mls',$mls);
        return view();
    }
    //删除会员级别
    public function del()
    {
    	$id = input('id');
    	if(Db::name('member_level')->delete($id)){
    		$this->success('删除会员级别成功！','member_level/lst');
    	}else
    	{
    		$this->error('删除会员级别失败！');
    	}
        return view();
    }


}
