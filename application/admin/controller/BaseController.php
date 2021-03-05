<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class BaseController extends Controller{

	//定义公共参数
	public $config;

	public function _initialize(){
		if(!session('useradmin')){
			$this->error('请先登录系统','admin/Login/login');
		}
		$this->_getConfs(); //获取配置项

	}

	//系统设置项
	public function _getConfs(){
		if(cache('configs')){
			$configs = cache('configs');
		}else{
			$configs = model('Conf')->getConfs();
			if($this->config['cache'] == '是'){
				cache('configs',$configs,$this->config['cache_time']);
			}
		}
		$this->config = $configs;
		$this->assign('configs',$configs);
	}
}