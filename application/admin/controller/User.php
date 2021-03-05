<?php
namespace app\admin\controller;
use think\Db;
use think\Validate;
class User extends BaseController
{
	//显示用户列表
    public function lst()
    {
    	$userRes = Db::name('user')->alias('u')->join('member_level ml','u.points >= ml.bom_point AND u.points <= ml.top_point')->field('u.*,ml.level_name,ml.rate')->order('id DESC')->paginate(10);
    	$this->assign('userRes',$userRes);
        return view();
    }
    //添加用户列表
    public function add()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            $data['username'] = trim($data['username']);
            $data['password'] = trim($data['password']);
            $data['register_time']     = time();
            //验证
            $validate = validate('user');
            if(!$validate->check($data)){
                $arr = ['error' => 0,'message' =>$validate->getError()];
                return json($arr);
            }
            //密码MD5加密
            $data['password'] = md5($data['password']);
    		$add  = Db::name('user')->insert($data);
    		if($add)
    		{
    			return 1;  //添加成功
    		}else
    		{
    			return 2;   //添加失败
    		}
    		return;
    	}
        return view();
    }
    //修改用户列表
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            $data['username'] = trim($data['username']);
            if($data['password']){
                $data['password'] = trim($data['password']);
                $data['password'] = md5($data['password']);
            }else{
                unset($data['password']);
            }
            
    		if(Db::name('user')->update($data) !== false){
    			$this->success('修改用户列表成功','user/lst');
    		}else{
    			$this->error('修改用户列表失败！');
    		}
    		return;
    	}
    	//赋值
    	$id = input('id');
    	$users = Db::name('user')->find($id);
    	$this->assign('users',$users);
        return view();
    }
    //删除用户列表
    public function del()
    {
    	$id = input('id');
        $orderRes = Db::name('order')->where('user_id',$id)->select();
        if($orderRes){
            return 2;   //该用户存在订单，不能被删除
        }
    	if(Db::name('user')->delete($id)){
    		return 1;
    	}else
    	{
    		return 0;
    	}
        return view();
    }


}
