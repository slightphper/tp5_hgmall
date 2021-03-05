<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Base extends Controller
{
	public $config; //定义公共配置项，使得控制器也可以使用
	public function _initialize(){
		$this->_getFooterArts(); //底部信息
		$this->_getNav();	  //导航
		$this->_getConfs();	 //网站配置
		$this->_getCates();  //获取分类信息
		$this->_getGuessLike(); //获取‘猜你喜欢’商品
		$this->_getHotGoods(); //获取‘热销’商品
	}

	//获取分类
	public function _getCates(){
		$cateRes = model('Category')->getCates();
		$this->assign([
			'cateRes' => $cateRes,
			]);
	}

	//底部文章(消费者帮助信息，网站信息)
	private function _getFooterArts(){
		$articleModel = model('Article');
		if(cache('helpCateRes')){
			$helpCateRes = cache('helpCateRes');
		}else{
			$helpCateRes = $articleModel->getFooterArts();
			if($this->config['cache'] == '是'){
           		cache('helpCateRes',$helpCateRes,$this->config['cache_time']);
            }
		}
		if(cache('shopInfoRes')){
			$shopInfoRes = cache('shopInfoRes');
		}else{
			$shopInfoRes = $articleModel->getShopInfo();
			if($this->config['cache'] == '是'){
           		cache('shopInfoRes',$shopInfoRes,$this->config['cache_time']);
            }
		}
		// halt($helpCateRes);
		$this->assign([
			'helpCateRes' => $helpCateRes,
			'shopInfoRes' => $shopInfoRes,
			]);
	}

	//获取导航栏信息
	private function _getNav(){
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
		// halt($navRes);
		$this->assign([
			'navRes' => $navRes,
			]);
	}

	//获取配置项信息
	private function _getConfs(){
		if(cache('configs')){
			$configs = cache('configs');
		}else{
			$configs = model('Conf')->getConfs();
			if($this->config['cache'] == '是'){
           		cache('configs',$configs,$this->config['cache_time']);
            }
		}
		$this->config = $configs;
		$this->assign([
			'configs' => $configs,
			]);
		// halt($configs);
	}


	//获取猜你喜欢商品
	public function _getGuessLike(){
		if(cache('GuessLike')){
			$GuessLike = cache('GuessLike');
		}else{
			$GuessLike = model('Goods')->getRecPosGoods(7,6);
			if($this->config['cache'] == '是'){
				cache('GuessLike',$GuessLike,$this->config['cache_time']);
			}
		}
		$this->assign('GuessLike',$GuessLike);
		// halt($GuessLike);
	}

	//获取热卖商品（商品详情、分类页展示）
	public function _getHotGoods(){	
		if(cache('hotGoodsRes')){
			$hotGoodsRes = cache('hotGoodsRes');
		}else{
			$hotGoodsRes = model('Goods')->getRecPosGoods(2,4);
			if($this->config['cache'] == '是'){
				cache('hotGoodsRes',$hotGoodsRes,$this->config['cache_time']);
			}
		}
		$this->assign('hotGoodsRes',$hotGoodsRes);
    	// halt($hotGoodsRes);
	}

}