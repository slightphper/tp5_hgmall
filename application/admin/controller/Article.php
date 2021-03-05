<?php
namespace app\admin\controller;
use think\Db;
use catetree\Catetree;
class Article extends BaseController
{
	//显示文章
    public function lst()
    {
    	$artRes = Db::name('article')->alias('a')->join('cate c','a.cate_id=c.id')
                    ->field('a.*,c.cate_name')
                    ->order('a.id DESC')->paginate(30);
    	$this->assign('artRes',$artRes);
        return view();
    }
    //添加文章
    public function add()
    {
        $cate = new Catetree();
    	if(request()->isPost()){
    		$data = input('post.');
            $data['addtime'] = time();
    		//判断网址是否有协议头
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		//图片上传
    		if($_FILES['thumb']['tmp_name']){
    			$data['thumb'] = $this->upload();
    		}
    		$add  = Db::name('article')->insert($data);
    		if($add)
    		{
    			$this->success('添加文章成功','article/lst');
    		}else
    		{
    			$this->error('添加文章失败');
    		}
    		return;
    	}
        $cateRes = Db::name('cate')->order('sort ASC')->select();
        $cateRes =  $cate->catetree($cateRes);
        $this->assign([
            'cateRes' => $cateRes,
            ]);
        return view();
    }

    //修改文章
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
    		//处理url
    		if($data['link_url'] && stripos($data['link_url'],'http://') === false){
    			$data['link_url'] = 'http://'.$data['link_url'];
    		}
    		//上传图片
    		if($_FILES['thumb']['tmp_name']){
    			//删除之前上传图片
    			$oldarticles = Db::name('article')->field('thumb')->find($data['id']);
    			$oldarticleImg = IMG_UPLOADS.'/article/'.$oldarticles['thumb'];
    			if(file_exists($oldarticleImg)){
    				@unlink($oldarticleImg);
    			}
    			$data['thumb'] = $this->upload();
    		}
    		if(Db::name('article')->update($data) !== false){
    			$this->success('修改文章成功','article/lst');
    		}else{
    			$this->error('修改文章失败！');
    		}
    		return;
    	}
    	//修改赋值
    	$id = input('id');
    	$arts = Db::name('article')->find($id);
        //分类
        $cate = new Catetree();
        $cateRes = Db::name('cate')->order('sort ASC')->select();
        $cateRes =  $cate->catetree($cateRes);
        $this->assign([
            'cateRes' => $cateRes,
            'arts'    => $arts,
            ]);
        return view();
    }
    //删除文章
    public function del()
    {
    	$id       = input('id');
        $article  = Db::name('article');
        //删除文章对应的图片
        $arts     = $article->field('thumb')->find($id);
        $thumbSrc = IMG_UPLOADS.'/article/'.$arts['thumb'];
        if(file_exists($thumbSrc)){
            @unlink($thumbSrc);
        }
    	if($article->delete($id)){
    		$this->success('删除文章成功！','article/lst');
    	}else
    	{
    		$this->error('删除文章失败！');
    	}
        return view();
    }

    //图片上传
    public function upload()
    {
        //获取表单上传文件
        $file = request()->file('thumb');
        //移动到 /public/static/uploads/article下
    	 if($file){
    	    	$info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'uploads'.DS.'article');
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

    //ueditor 图片管理
    public function imgMange(){
        //定义自定义函数，common.php中
        $_files = my_scandir(); 
        $files = array();
        foreach ($_files as $k => $v) {
            if(is_array($v)){
                foreach ($v as $k1 => $v1) {
                    $v1 = str_replace(UEDITOR, HTTP_UEDITOR, $v1);
                    $files[] = $v1;
                }
            }else{
                $v = str_replace(UEDITOR, HTTP_UEDITOR, $v);
                $files[] = $v;
            }
        }
       // halt($files);
       $this->assign([
            'imgRes' => $files,
        ]);
        return view();
    }

    //删除图片
    public function delimg(){
        $imgsrc = input('imgsrc');
        $imgsrc = DEL_UEDITOR.$imgsrc;
        if(file_exists($imgsrc)){
            if(@unlink($imgsrc)){
                echo 1;
            }else{ echo 2;}
        }else{
            echo 3;
        }
    }

}
