<?php
namespace app\index\controller;
use think\Db;
class Goods extends Base
{
    public function index($id)
    {
    	//获取商品的基本信息
    	$goodsInfo = Db::name('goods')->find($id);
    	//获取当前商品主图信息
    	$goodsThumbArr = array();
    	if($goodsInfo['og_thumb']){
    		$goodsThumbArr['sm_photo'] = $goodsInfo['sm_thumb'];
    		$goodsThumbArr['mid_photo'] = $goodsInfo['mid_thumb'];
    		$goodsThumbArr['big_photo'] = $goodsInfo['big_thumb'];
    		$goodsThumbArr['og_photo'] = $goodsInfo['og_thumb'];
    	}
    	//获取当前商品相册信息
    	$goodsPhotoRes = Db::name('goods_photo')->field('sm_photo,mid_photo,big_photo,og_photo')->where('goods_id','=',$id)->select();
    	//把商品表的主图存为数组 放到相册表(头部)  
        //*:因主图和相册不在同一文件里，所以要分开赋值
    	// array_unshift($goodsPhotoRes,$goodsThumbArr);
        //获取当前商品的属性信息（单选/唯一）
        $goodsAttrs = Db::name('goods_attr')->alias('ga')->field('ga.*,a.attr_name,a.attr_type')->join('attr a','ga.attr_id = a.id')->where('ga.goods_id','=',$id)->select();
        $radioAttrArr = array();
        $uniAttrArr = array();
        foreach ($goodsAttrs as $k => $v) {
            if($v['attr_type'] == 1){
                //分离单选属性后，在把属性id(属性名)分组
                $radioAttrArr[$v['attr_id']][] = $v;
            }else{
                $uniAttrArr[] = $v;
            }
        }
        // dump($goodsPhotoRes);
        // dump($radioAttrArr);
        // echo "<hr>";
        // halt($uniAttrArr);
        
        //获取商品面包屑导航
        $catesCache = 'cates_'.$id; 
        if(cache($catesCache)){
            $cates = cache($catesCache);
        }else{
            $catePosition = model('Category')->getPosition($goodsInfo['category_id']);
            if($this->config['cache'] == '是'){
                cache($catesCache,$catePosition,$this->config['cache_time']);
            }
        }

    	$this->assign([
    		'goodsInfo'     => $goodsInfo, 
            'goodsThumbArr' => $goodsThumbArr,  //商品主图
    		'goodsPhotoRes' => $goodsPhotoRes,  //商品相册
            'radioAttrArr'  => $radioAttrArr,   //单选
            'uniAttrArr'    => $uniAttrArr,     //唯一
            'catePosition'  => $catePosition,   //面包屑导航
    		]);
        return view('goods');
    }

    //获取当前商品的会员价格
    public function ajaxGetMemberPrice($goods_id){
        $price = model('Goods')->getMemberPrice($goods_id);
        return json($price);
    }

}
