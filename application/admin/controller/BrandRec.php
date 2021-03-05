<?php
namespace app\admin\controller;
use think\Db;
class BrandRec extends BaseController
{
	//显示分类推荐品牌
    public function lst()
    {
    	$brandRec = Db::name('brand_rec')->alias('br')
                    ->field('br.*,c.cate_name,GROUP_CONCAT(b.brand_name) brand_name')
                    ->join('category c','br.category_id = c.id')
                    ->join('brand b','find_in_set(b.id,br.brands_id)','LEFT')
                    ->order('br.category_id ASC')->group('br.id')->paginate(8);
    	$this->assign('brandRec',$brandRec);
        return view();
    }
    //添加分类推荐品牌
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//判断网址是否有协议头
    		if($data['pro_url'] && stripos($data['pro_url'],'http://') === false){
    			$data['pro_url'] = 'http://'.$data['pro_url'];
    		}
    		//图片上传
    		if($_FILES['pro_img']['tmp_name']){
    			$data['pro_img'] = $this->upload();
    		}
            //处理品牌
            if(isset($data['brands_id'])){
                $data['brands_id'] = implode(',', $data['brands_id']);
            }
    		$add = Db::name('brand_rec')->insert($data);
    		if($add)
    		{
    			$this->success('添加分类推荐品牌成功','brandRec/lst');
    		}else
    		{
    			$this->error('添加分类推荐品牌失败');
    		}
    		return;
    	}
        //获取关联分类
        $categoryRes = Db::name('category')->where('pid','=',0)->select();
        //获取所推荐品牌(只取有logo)
        $brandRes = Db::name('brand')->field('id,brand_name')->where('brand_img','neq','')->select();
        //获取已选分类（要求一个顶级分类只能有一个分类品牌推荐）
        $_cateSelected = Db::name('brand_rec')->field('category_id')->select();
        $cateSelected = array();
        foreach ($_cateSelected as $k => $v) {
            $cateSelected[] = $v['category_id']; 
        }
        // halt($cateSelected);
        $this->assign([
            'categoryRes' => $categoryRes,
            'brandRes'    => $brandRes,
            'cateSelected'=> $cateSelected, //已选顶级分类
            ]);
        return view();
    }
    //修改分类推荐品牌
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//处理url
    		if($data['pro_url'] && stripos($data['pro_url'],'http://') === false){
    			$data['pro_url'] = 'http://'.$data['pro_url'];
    		}
    		//上传图片
    		if($_FILES['pro_img']['tmp_name']){
    			//删除之前上传图片
    			$oldbrand_recs = Db::name('brand_rec')->field('pro_img')->find($data['id']);
    			$oldbrand_recImg = IMG_UPLOADS.'brand_rec/'.$oldbrand_recs['pro_img'];
    			if(file_exists($oldbrand_recImg)){
    				@unlink($oldbrand_recImg);
    			}
    			$data['pro_img'] = $this->upload();
    		}
            if(isset($data['brands_id'])){
                $data['brands_id'] = implode(',',$data['brands_id']);
            }else{
                $data['brands_id'] = '';
            }
    		if(Db::name('brand_rec')->update($data) !== false){
    			$this->success('修改分类推荐品牌成功','brandRec/lst');
    		}else{
    			$this->error('修改分类推荐品牌失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$brands = Db::name('brand_rec')->find($id);
        $brands['brands_id'] = explode(',',$brands['brands_id']);
        //获取关联分类
        $categoryRes = Db::name('category')->where('pid','=',0)->select();
        //获取所推荐品牌(只取有logo)
        $brandRes = Db::name('brand')->field('id,brand_name')->where('brand_img','neq','')->select();
        // halt($brands);
    	$this->assign([
            'brands' => $brands,
            'categoryRes' => $categoryRes,
            'brandRes'    => $brandRes,
            ]);
        return view();
    }
    //删除分类推荐品牌
    public function del()
    {
    	$id = input('id');
        $brandRec = Db::name('brand_rec')->field('pro_img')->find($id);
        if($brandRec['pro_img']){
            $proImg = IMG_UPLOADS.'brand_rec/'.$brandRec['pro_img'];
            if(file_exists($proImg)){
                @unlink($proImg);
            }
        }
    	if(Db::name('brand_rec')->delete($id)){
    		$this->success('删除分类推荐品牌成功！','brandRec/lst');
    	}else
    	{
    		$this->error('删除分类推荐品牌失败！');
    	}
        return view();
    }

    //图片上传
    public function upload(){
    //获取表单上传文件
    $file = request()->file('pro_img');
    //移动到 /public/static/uploads下
	 if($file){
	    	$info = $file->move(IMG_UPLOADS.'brand_rec/');
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
