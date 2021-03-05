<?php
namespace app\admin\controller;
use think\Db;
class Brand extends BaseController
{
	//显示品牌
    public function lst()
    {
    	$brandRes = Db::name('brand')->order('id DESC')->paginate(20);
    	$this->assign('brandRes',$brandRes);
        return view();
    }
    //添加品牌
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//判断网址是否有协议头
    		if($data['brand_url'] && stripos($data['brand_url'],'http://') === false){
    			$data['brand_url'] = 'http://'.$data['brand_url'];
    		}
    		//图片上传
    		if($_FILES['brand_img']['tmp_name']){
    			$data['brand_img'] = $this->upload();
    		}
    		$add  = Db::name('brand')->insert($data);
    		if($add)
    		{
                // echo "<script>location.href='lst';alert('添加品牌成功');</script>";
    			$this->success('添加品牌成功','brand/lst');
    		}else
    		{
    			$this->error('添加品牌失败');
    		}
    		return;
    	}
        return view();
    }
    //修改品牌
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//处理url
    		if($data['brand_url'] && stripos($data['brand_url'],'http://') === false){
    			$data['brand_url'] = 'http://'.$data['brand_url'];
    		}
    		//上传图片
    		if($_FILES['brand_img']['tmp_name']){
    			//删除之前上传图片
    			$oldBrands = Db::name('brand')->field('brand_img')->find($data['id']);
    			$oldBrandImg = IMG_UPLOADS.'/brand/'.$oldBrands['brand_img'];
    			if(file_exists($oldBrandImg)){
    				@unlink($oldBrandImg);
    			}
    			$data['brand_img'] = $this->upload();
    		}
    		if(Db::name('brand')->update($data) !== false){
    			$this->success('修改品牌成功','Brand/lst');
    		}else{
    			$this->error('修改品牌失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$brands = Db::name('brand')->find($id);
    	$this->assign('brands',$brands);
        return view();
    }
    //删除品牌
    public function del()
    {
    	$id = input('id');
        $brandIMg = Db::name('brand')->field('brand_img')->find($id);
        $brandIMg = IMG_UPLOADS.'/brand/'.$brandIMg['brand_img'];
        if(file_exists($brandIMg)){
            @unlink($brandIMg);
        }
    	if(Db::name('brand')->delete($id)){
    		$this->success('删除品牌成功！','brand/lst');
    	}else
    	{
    		$this->error('删除品牌失败！');
    	}
        return view();
    }

    //图片上传
    public function upload(){
    //获取表单上传文件
    $file = request()->file('brand_img');

    //移动到 /public/static/uploads下
	 if($file){
	    	$info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'uploads'.DS.'brand');
	    if($info){
            // dump($info);
	    	return $info->getSaveName();
	    }else
	    {
	    	//上传错误信息
	    	echo $file->getError();
	    	die();
	    }

    }
	}

}
