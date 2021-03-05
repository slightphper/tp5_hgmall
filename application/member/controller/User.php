<?php 
namespace app\member\controller;
use app\index\controller\Base;
use think\Db;
class User extends Base{
	public $config; //声明公共配置，别的控制器也可以使用

	//显示用户中心首页
	public function index(){

		//获取所有配置项
		if(cache('configs')){
			$configs = cache('configs');
		}else{
			$configs = model('conf')->getConfs();
			$this->config = $configs;
			if($this->config['cache'] == '是'){
				cache('configs',$configs,$this->config['cache_time']);
			}
		}

		//获取导航栏信息
		if(cache('navRes')){
			$navRes = cache('navRes');
		}else{
			$_navRes = Db::name('nav')->order('sort ASC')->select();
			$navRes = array();
			foreach ($_navRes as $k => $v) {
				$navRes[$v['pos']][] = $v;
			}
			if($this->config['cache'] == '是'){
           		cache('navRes',$navRes,$this->config['cache_time']);
            }
		}
		
		$this->assign([
			'navRes' => $navRes,
			'configs'=> $configs,
			]);
		// halt($configs);
		return view();
	}


}