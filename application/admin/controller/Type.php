<?php
namespace app\admin\controller;
use think\Db;
class Type extends BaseController
{
	//显示商品类型
    public function lst()
    {
    	$typeRes = Db::name('type')->order('id DESC')->paginate(10);
    	$this->assign('typeRes',$typeRes);
        return view();
    }
    //添加商品类型
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		$add  = Db::name('type')->insert($data);
    		if($add)
    		{
    			$this->success('添加商品类型成功','type/lst');
    		}else
    		{
    			$this->error('添加商品类型失败');
    		}
    		return;
    	}
        return view();
    }
    //修改商品类型
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		if(Db::name('type')->update($data) !== false){
    			$this->success('修改商品类型成功','type/lst');
    		}else{
    			$this->error('修改商品类型失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$types = Db::name('type')->find($id);
    	$this->assign('types',$types);
        return view();
    }
    //删除商品类型
    public function del()
    {
    	$id = input('id');
        Db::name('attr')->where(array('type_id'=>$id))->delete();
    	if(Db::name('type')->delete($id)){
    		$this->success('删除商品类型成功！','type/lst');
    	}else
    	{
    		$this->error('删除商品类型失败！');
    	}
        return view();
    }


}
