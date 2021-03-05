<?php
namespace app\admin\controller;
use think\Db;
use catetree\Catetree;
class AlternateImg extends BaseController
{
	//显示首页轮播
    public function lst()
    {
        $sort = new Catetree();
        if(request()->isPost()){
            $data = input('post.');
            $sort->cateSort($data['sort'],Db::name('alternate_img'));
        }
    	$alterImgRes = Db::name('alternate_img')->order('sort ASC')->paginate(10);
    	$this->assign([
            'alterImgRes' => $alterImgRes,
            ]);
        return view();
    }
    //添加首页轮播
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//判断网址是否有协议头
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		//图片上传
    		if($_FILES['img_src']['tmp_name']){
    			$data['img_src'] = $this->upload();
    		}
    		$add = Db::name('alternate_img')->insert($data);
    		if($add)
    		{
    			$this->success('添加首页轮播成功','AlternateImg/lst');
    		}else
    		{
    			$this->error('添加首页轮播失败');
    		}
    		return;
    	}
        return view();
    }
    //修改首页轮播
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//处理url
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		//上传图片
    		if($_FILES['img_src']['tmp_name']){
    			//删除之前上传图片
    			$oldAlterImg = Db::name('alternate_img')->field('img_src')->find($data['id']);
    			$oldAlterImg = IMG_UPLOADS.'alternate/'.$oldAlterImg['img_src'];
    			if(file_exists($oldAlterImg)){
    				@unlink($oldAlterImg);
    			}
    			$data['img_src'] = $this->upload();
    		}
    		if(Db::name('alternate_img')->update($data) !== false){
    			$this->success('修改首页轮播成功','AlternateImg/lst');
    		}else{
    			$this->error('修改首页轮播失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$alterImgRes = Db::name('alternate_img')->find($id);
    	$this->assign([
            'alterImgRes' => $alterImgRes,
            ]);
        return view();
    }
    //删除首页轮播
    public function del()
    {
    	$id = input('id');
        $alterImg = Db::name('alternate_img')->field('img_src')->where('id','=',$id)->find();
        if($alterImg['img_src']){
            $alterImg = IMG_UPLOADS.'alternate/'.$alterImg['img_src'];
            if(file_exists($alterImg)){
                @unlink($alterImg);
            }
        }
    	if(Db::name('alternate_img')->delete($id)){
    		$this->success('删除首页轮播成功！','AlternateImg/lst');
    	}else
    	{
    		$this->error('删除首页轮播失败！');
    	}
        return view();
    }

    //图片上传
    public function upload(){
    //获取表单上传文件
    $file = request()->file('img_src');
    //移动到 /public/static/uploads下
	 if($file){
	    	$info = $file->move(IMG_UPLOADS.'alternate');
	    if($info){
            //前台轮播图不能识别保存的 '\' 在这里替换/保存
            $imgSrc = $info->getSaveName();
            $imgSrc = str_replace('\\','/',$imgSrc);
	    	return $imgSrc;
	    }else
	    {
	    	//上传错误信息
	    	echo $file->getError();
	    	die();
	    }
    }
	}

}
