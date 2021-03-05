<?php
namespace app\admin\controller;
use think\Db;
class Attr extends BaseController
{
	//显示商品属性
    public function lst()
    {
        $typeId = input('type_id');
        if($typeId){
            $map['type_id'] = ['=',$typeId];
        }else{
            $map = 1;
        }
    	$attrRes = Db::name('attr')->alias('a')
            ->field('a.id,a.attr_name,a.attr_type,a.attr_values,t.type_name')
            ->join('type t','a.type_id = t.id')
            ->where($map)->order('t.type_name')->paginate(20);
        // halt($attrRes);
    	$this->assign('attrRes',$attrRes);
        return view();
    }   
    //添加商品属性
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            $data['attr_values'] = str_replace('，',',',$data['attr_values']);
    		$add  = Db::name('attr')->insert($data);
    		if($add)
    		{
    			$this->success('添加商品属性成功','attr/lst');
    		}else
    		{
    			$this->error('添加商品属性失败');
    		}
    		return;
    	}
        //获取属性的类型
        $typeRes = Db::name('type')->select();
        $this->assign('typeRes',$typeRes);
        return view();
    }
    //修改商品属性
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            $data['attr_values'] = str_replace('，',',',$data['attr_values']);
    		if(db('attr')->update($data) !== false){
    			$this->success('修改商品属性成功','attr/lst');
    		}else{
    			$this->error('修改商品属性失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$attrs = Db::name('attr')->find($id);
        //获取属性的类型
        $typeRes = Db::name('type')->select();
    	$this->assign([
            'attrs'  => $attrs,
            'typeRes'=> $typeRes,
            ]);
        return view();

    }
    //删除商品属性
    public function del()
    {
    	$id = input('id');
    	if(Db::name('attr')->delete($id)){
    		$this->success('删除商品属性成功！','attr/lst');
    	}else
    	{
    		$this->error('删除商品属性失败！');
    	}
        return view();
    }

    //异步获取指定类型下属性
    public function ajaxGetAttr(){
        $typeId = input('type_id');
        $attrRes = Db::name('attr')->where(array('type_id'=>$typeId))->select();
        echo json_encode($attrRes);
    }


}
