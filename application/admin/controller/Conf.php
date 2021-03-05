<?php
namespace app\admin\controller;
use think\Db;
class Conf extends BaseController
{
	//显示配置
    public function lst()
    {
        $conf = Db::name('conf');
        if(request()->isPost()){
            $data = input('post.');
            // halt($data);
            foreach ($data as $k => $v) {
                $conf->update(['id'=>$k,'sort'=>$v]);
            }
        }
    	$confRes = $conf->order('conf_type,sort ASC')->paginate(20);
    	$this->assign('confRes',$confRes);
        return view();
    }
    //添加配置
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            //如果是多选，替换中文逗号，
            if($data['form_type'] == 'redio' || $data['form_type'] == 'checkbox' || $data['form_type'] == 'select'){
                $data['values'] = str_replace('，', ',', $data['values']);
                $data['value'] = str_replace('，', ',', $data['value']);
            }
    		$add  = Db::name('conf')->insert($data);
    		if($add)
    		{
    			$this->success('添加配置成功','conf/lst');
    		}else
    		{
    			$this->error('添加配置失败');
    		}
    		return;
    	}
        return view();
    }
    //修改配置
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
              //如果是多选，替换中文逗号，
            if($data['form_type'] == 'redio' || $data['form_type'] == 'checkbox' || $data['form_type'] == 'select'){
                $data['values'] = str_replace('，', ',', $data['values']);
                $data['value'] = str_replace('，', ',', $data['value']);
            }
    		if(Db::name('conf')->update($data) !== false){
    			$this->success('修改配置成功','conf/lst');
    		}else{
    			$this->error('修改配置失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$confs = Db::name('conf')->find($id);
    	$this->assign('confs',$confs);
        return view();
    }
    //删除配置
    public function del()
    {
    	$id = input('id');
        $conf = Db::name('conf')->field('form_type,value')->find($id);
        if($conf['form_type'] == 'file'){
            $confImg = IMG_UPLOADS.'conf/'.$conf['value'];
            if(file_exists($confImg)){
                @unlink($confImg);
            }
        }
    	if(Db::name('conf')->delete($id)){
    		$this->success('删除配置成功！','conf/lst');
    	}else
    	{
    		$this->error('删除配置失败！');
    	}
        return view();
    }

    //添加配置项
    public function conflist(){
        $conf = db('conf');        
        if(request()->isPost()){
            $data = input('post.');
            // halt($data);
            //复选框空选问题
            $checkFields2D = $conf->field('ename')->where(array('form_type'=>'checkbox'))->select();
            //改为一维数组
            $checkFields = array();
            if($checkFields2D){
                foreach ($checkFields2D as $k => $v) {
                    $checkFields[] = $v['ename'];
                }
            }
            //所有字段组成数组
            $allFields = array();
            //处理文字数据
            foreach ($data as $k => $v) {
                $allFields[] = $k;
                if(is_array($v)){
                    $value = implode(',', $v);
                    $conf->where(array('ename'=>$k))->update(['value'=>$value]);
                }else{
                    $conf->where(array('ename'=>$k))->update(['value'=>$v]);
                }
            }
            // halt($allFields);
            //判断复选框是否有选中
            foreach ($checkFields as $k => $v) {
                if(!in_array($v,$allFields)){
                    $conf->where(array('ename'=>$v))->update(['value'=> '']);
                }
            }
            //图片上传
            if($_FILES){
                foreach ($_FILES as $k => $v) {
                    if($v['tmp_name']){
                        $imgs = $conf->field('value')->where(array('ename'=>$k))->find();
                        if($imgs['value']){
                            //拼装被替换图地址
                            $oldImg = IMG_UPLOADS.'conf/'.$imgs['value'];
                            if(file_exists($oldImg)){
                                @unlink($oldImg);
                            }
                        }
                        $imgSrc = $this->upload($k);
                        $conf->where(array('ename'=>$k))->update(['value'=>$imgSrc]);
                    }
                }
            }
            $this->success('配置成功');
        }
        $ShopConfRes  = $conf->where(array('conf_type'=>1))->order('sort asc')->select();
        $GoodsConfRes = $conf->where(array('conf_type'=>2))->order('sort asc')->select();
        $this->assign([
            'ShopConfRes' => $ShopConfRes,
            'GoodsConfRes'=> $GoodsConfRes,
            ]);

        return view();
    }


    //图片上传
    public function upload($imgName){
        //获取表单上传文件
        $file = request()->file($imgName);

        //移动到 /public/static/uploads/conf/下
         if($file){
                $info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'uploads'.DS.'conf');
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
