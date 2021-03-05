<?php
namespace app\index\controller;
use think\Db;
class Index extends Base
{
    public function index()
    {	
        // echo session('member_id');
        // echo session('member_rate');
        //首页轮播图展示
        if(cache('alterImgRes')){
            $alterImgRes = cache('alterImgRes');
        }else{
            $alterImgRes = model('Index')->getAlternateImgs();
            if($this->config['cache'] == '是'){
                cache('alterImgRes',$alterImgRes,$this->config['cache_time']);
            }
        }		
    	
        //获取首页公告及促销文章
        if(cache('noticeArts')){
            $noticeArts = cache('noticeArts');
        }else{
            $noticeArts = model('Article')->getMainArts(19,3);//公告
            if($this->config['cache'] == '是'){
                cache('noticeArts',$noticeArts,$this->config['cache_time']);
            }
        }
        if(cache('saleArts')){
            $saleArts = cache('saleArts');
        }else{
            $saleArts = model('Article')->getMainArts(24,3);//促销
            if($this->config['cache'] == '是'){
                cache('saleArts',$saleArts,$this->config['cache_time']);
            }
        }
        
        //获取首页商品 （底部：还没逛够 栏目下）
        if(cache('indexGoodsRes')){
            $indexGoodsRes = cache('indexGoodsRes');
        }else{
            $indexGoodsRes = model('Goods')->getRecPosGoods(1,10);
            if($this->config['cache'] == '是'){
                cache('indexGoodsRes',$indexGoodsRes,$this->config['cache_time']);
            }
        }

    	//获取首页大分类推荐  顶级分类
        if(cache('categoryRec')){
            $categoryRec = cache('categoryRec');
        }else{
            $categoryRec = model('Category')->getRecCategorys(4,0);
            foreach ($categoryRec as $k => $v) {
            $categoryRec[$k]['children'] = model('Category')->getRecCategorys(5,$v['id']);

            //获取分类推荐下--> 精选推荐商品(二级分类及以下)
            foreach ($categoryRec[$k]['children'] as $k2 => $v2) {
                $categoryRec[$k]['children'][$k2]['bestGoods'] = model('Goods')->getIndexRecposGoods($v2['id'],6);
                // dump($categoryRec[$k]['children']);
               /*
                * 以下代码被封装到GoodsModel里面,精选推荐和最新推荐代码可重用
                * $cateTree = new Catetree();
                $cateSonId = $cateTree->childrenids($v2['id'],Db::name('category'));
                $cateSonId[] = $v2['id'];
                //推荐位表里查询符合条件的商品信息
                $_bestRecposGoods = Db::name('rec_item')->where(array('value_type'=>1,'recpos_id'=>6))->select();
                $bestRecposGoods = array();
                foreach ($_bestRecposGoods as $k3 => $v3) {
                    $bestRecposGoods[] = $v3['value_id'];
                }
                $map['category_id'] = array('IN',$cateSonId);
                $map['id'] = array('IN',$bestRecposGoods);
                $categoryRec[$k]['children'][$k2]['bestGoods'] = Db::name('goods')->where($map)->order('id desc')->limit(6)->select();*/
            }

            //获取分类推荐下--> 最新推荐商品
            $categoryRec[$k]['newRecGoods'] = model('Goods')->getIndexRecposGoods($v['id'],3);
            /* 
             * 以下代码被封装到GoodsModel里面,精选推荐和最新推荐代码可重用
            $cateTree = new Catetree();
            $cateSonId = $cateTree->childrenids($v['id'],Db::name('category'));
            $cateSonId[] = $v['id'];
            //推荐位表里查询符合条件的商品信息
            $_newRecposGoods = Db::name('rec_item')->where(array('value_type'=>1,'recpos_id'=>3))->select();
            $newRecposGoods = array();
            foreach ($_newRecposGoods as $k1 => $v1) {
                $newRecposGoods[] = $v1['value_id'];
            }
            $map['category_id'] = array('IN',$cateSonId);
            $map['id'] = array('IN',$newRecposGoods);
            $categoryRec[$k]['newRecGoods'] = Db::name('goods')->where($map)->order('id desc')->limit(6)->select();
            dump($newRecGoods);echo '<br>-----------------'.$v['id'];
            */
            //获取该顶级分类下的关联推荐品牌 brand_rec
            $categoryRec[$k]['brands'] = model('Category')->getBrandRec($v['id']);
            //获取该顶级分类下左侧广告位信息
            $categoryRec[$k]['advImg'] = model('CategoryAdv')->getCategoryAdv($v['id']);
            }
            if($this->config['cache'] == '是'){
                cache('categoryRec',$categoryRec,$this->config['cache_time']);
            }
        }
		// halt($categoryRec); 
    	$this->assign([
    		'show_nav'     => 1,            //首页分类默认展开
            'alterImgRes'  => $alterImgRes, //轮播图
            'noticeArts'   => $noticeArts,  //公告
            'saleArts'     => $saleArts,    //促销
    		'categoryRec'  => $categoryRec, //推荐分类位
            'indexGoodsRes'=> $indexGoodsRes, //首页商品位（还没逛够）
    		]);
        return view();
    }

    
}
