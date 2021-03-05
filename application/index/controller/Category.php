<?php
namespace app\index\controller;
use think\Db;
class Category extends Base
{
    public function index()
    {
        $cate_id = input('id');
        $cateName = Db::name('category')->field('cate_name')->find($cate_id);
        //获取面包屑
        $catePosition = model('Category')->getPosition($cate_id);

        //获取分类商品
        $cateGoods = model('Category')->getCateGoodsAll($cate_id);

        $this->assign([
            'cateName'     => $cateName['cate_name'],  //分类名
            'catePosition' => $catePosition,       //面包屑导航
            'cateGoods'    => $cateGoods,
            'cateGoodsList'=> $cateGoods,
            ]);
        return view('category');
    }

    //首页ajax分类
    public function getCateInfo($id){
    	//获取二级三级子分类
    	$cateRes = model('Category')->getSonCates($id);
    	$data = array();
    	$cat = '';
    	foreach ($cateRes as $k => $v) {
    		$cat .= '<dl class="d1_fore1">
					<dt><a href="'.url('index/Category/index',['id'=>$v['id']]).'" target="_blank">'.$v['cate_name'].'</a></dt>';
			$cat .= '<dd>';		
			foreach ($v['children'] as $k1 => $v1) {
				$cat .=	'<a href="'.url('index/Category/index',['id'=>$v1['id']]).'" target="_blank">'.$v1['cate_name'].'</a>';
				}
			$cat .= '</dd>';
			$cat .='</dl>
					<div class="item-brands"><ul></ul></div>
					<div class="item-promotions"></div>';  			
    	}
        //获取该分类推荐分类     *:因为用户输入url可能导致分类跳转到别处，所以目前先把连接写死
        $cateRec = model('Category')->getCategoryRec($id);
        $channels = '';
        foreach ($cateRec as $k1 => $v1) {
            $channels .= '<a href="javascript:return false;">'.$v1['category_rec'].'</a>';
        }
        //获取该分类推荐品牌
        $brandRec = model('Category')->getBrandRec($id);
        $brandsContent = '';
        $brandsContent .= '<div class="cate-brand">';
            foreach ($brandRec['brands'] as $k2 => $v2) {
               $brandsContent .= 
                '<div class="img">
                    <a href="#" target="_blank" title="'.$v2['brand_name'].'">
                        <img src="'.config('view_replace_str.__uploads__').'/brand/'.$v2['brand_img'].'">
                    </a>
                </div>';
            }
        $brandsContent .= '</div>';
        //品牌广告位  
        $brandsContent .= 
            '<div class="cate-promotion">
                <a href="'.$brandRec['promotion']['pro_url'].'">
                    <img src="'.config('view_replace_str.__uploads__').'/brand_rec/'.$brandRec['promotion']['pro_img'].'" width="199" height="97">
                </a>
            </div>';
    	$data['topic_content'] = $channels;
    	$data['cat_content'] = $cat;
    	$data['brands_ad_content'] = $brandsContent;
    	$data['cat_id'] = $id;
    	return json($data);

    }
}
