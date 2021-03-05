<?php
namespace app\admin\controller;
use think\Db;
class Index extends BaseController
{
    public function index()
    {
        //首页详情
        $OrderAll = Db::name('order')->count();
        $GoodsAll = Db::name('goods')->count();
        $UserAll  = Db::name('User')->count();
        $this->assign([
            'OrderAll' => $OrderAll,
            'GoodsAll' => $GoodsAll,
            'UserAll'  => $UserAll,
            ]);
        return view();
    }

  	//清空缓存
    public function clearCache(){
    	if(cache(NULL)){
    		$this->success('缓存清除成功！');
    	}else{
    		$this->error('缓存清除失败！');
    	}
    }
}
