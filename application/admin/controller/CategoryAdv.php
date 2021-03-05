<?php
namespace app\admin\controller;
use think\Db;
class CategoryAdv extends BaseController
{
	//显示分类推荐左侧广告位
    public function lst()
    {
    	$CateAdv = Db::name('category_adv')->alias('ca')
                    ->field('ca.*,c.cate_name')
                    ->join('category c','ca.category_id = c.id')
                    ->order('ca.category_id ASC')->paginate(12);
    	$this->assign('CateAdv',$CateAdv);
        return view();
    }
    //添加分类推荐左侧广告位
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            //判断右侧是否有重复添加图片
            if($data['position'] == 'B' || $data['position'] == 'C'){
                $cateAdv = Db::name('category_adv')->where(array('category_id'=>$data['category_id'],'position'=>$data['position']))->select();
                if($cateAdv){
                    $this->error('当前位置只能添加一张图片！');
                }
            }
    		//判断网址是否有协议头
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		//图片上传
    		if($_FILES['img_src']['tmp_name']){
    			$data['img_src'] = $this->upload();
    		}else{
                $this->error('请上传图片');
            }
    		$add = Db::name('category_adv')->insert($data);
    		if($add)
    		{
    			$this->success('添加分类推荐左侧广告位成功','CategoryAdv/lst');
    		}else
    		{
    			$this->error('添加分类推荐左侧广告位失败');
    		}
    		return;
    	}
        //获取关联分类
        $categoryRes = Db::name('category')->where('pid','=',0)->select();
        $this->assign([
            'categoryRes' => $categoryRes,
            ]);
        return view();
    }
    //修改分类推荐左侧广告位
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            //判断右侧是否有重复添加图片  (修改不用判断了)
            // if($data['position'] == 'B' || $data['position'] == 'C'){
            //     $cateAdv = Db::name('category_adv')->where(array('category_id'=>$data['category_id'],'position'=>$data['position']))->select();
            //     if($cateAdv){
            //         $this->error('当前位置只能添加一张图片！');
            //     }
            // }
    		//处理url
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		//上传图片
    		if($_FILES['img_src']['tmp_name']){
    			//删除之前上传图片
    			$oldCateAdv = Db::name('category_adv')->field('img_src')->find($data['id']);
    			$oldCateAdvImg = IMG_UPLOADS.'category_adv/'.$oldCateAdv['img_src'];
    			if(file_exists($oldCateAdvImg)){
    				@unlink($oldCateAdvImg);
    			}
    			$data['img_src'] = $this->upload();
    		}
    		if(Db::name('category_adv')->update($data) !== false){
    			$this->success('修改分类推荐左侧广告位成功','CategoryAdv/lst');
    		}else{
    			$this->error('修改分类推荐左侧广告位失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$cateAdv = Db::name('category_adv')->find($id);
        //获取推荐分类
        $categoryRes = Db::name('category')->where('pid','=',0)->select();
    	$this->assign([
            'cateAdv' => $cateAdv,
            'categoryRes' => $categoryRes,
            ]);
        return view();
    }
    //删除分类推荐左侧广告位
    public function del()
    {
    	$id = input('id');
        $CategoryAdv = Db::name('category_adv')->field('img_src')->find($id);
        if($CategoryAdv['img_src']){
            $advImg = IMG_UPLOADS.'category_adv/'.$CategoryAdv['img_src'];
            if(file_exists($advImg)){
                @unlink($advImg);
            }
        }
    	if(Db::name('category_adv')->delete($id)){
    		$this->success('删除分类推荐左侧广告位成功！','CategoryAdv/lst');
    	}else
    	{
    		$this->error('删除分类推荐左侧广告位失败！');
    	}
        return view();
    }

    //图片上传
    public function upload(){
    //获取表单上传文件
    $file = request()->file('img_src');
    //移动到 /public/static/uploads下
	 if($file){
	    	$info = $file->move(IMG_UPLOADS.'category_adv/');
	    if($info){
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
