<?php
namespace app\admin\controller;
use think\Db;
class categoryRec extends BaseController
{
	//显示分类推荐词
    public function lst()
    {
    	$categoryRes = Db::name('category_rec')->alias('cr')->join('category c','cr.category_id = c.id')->field('cr.*,c.cate_name')->order('cr.category_id ASC')->paginate(15);
    	$this->assign('categoryRes',$categoryRes);
        return view();
    }
    //添加分类推荐词
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//判断网址是否有协议头
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		$add  = Db::name('category_rec')->insert($data);
    		if($add)
    		{
    			$this->success('添加分类推荐词成功','categoryRec/lst');
    		}else
    		{
    			$this->error('添加分类推荐词失败');
    		}
    		return;
    	}
        $categoryRes = model('Category')->where('pid','=',0)->select();
        $this->assign([
            'categoryRes' => $categoryRes,
            ]);
        return view();
    }
    //修改分类推荐词
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//处理url
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		if(Db::name('category_rec')->update($data) !== false){
    			$this->success('修改分类推荐词成功','categoryRec/lst');
    		}else{
    			$this->error('修改分类推荐词失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$cateRec = Db::name('category_rec')->find($id);
        //获取推荐分类
        $categoryRes = Db::name('category')->where('pid','=',0)->select();
    	$this->assign([
            'categoryRes' => $categoryRes,
            'cateRec'     => $cateRec,
            ]);
        return view();
    }
    //删除分类推荐词
    public function del()
    {
    	$id = input('id');
        if(Db::name('category_rec')->delete($id)){
            return 1;
        }else{ return 0;
        }
        return view();
    }

}
