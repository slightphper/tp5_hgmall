<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;
class Brand extends Controller{

	public function lst(){
		$data = array();
		$brandRes = Db::name('brand')->order('id desc')->paginate(11);
		$data['totalPage']   = $brandRes->lastPage();
		$brands = $brandRes->items();
		$html = '';
		$html .= '<div class="brand-list" id="recommend_brands"><ul>';
		foreach ($brands as $k => $v) {
			$html .= '<li><div class="brand-img"><a href="#" target="_blank"><img src="'.config('view_replace_str.__uploads__').'/brand/'.$v['brand_img'].'"></a></div><div class="brand-mash"></div></li>';
		}
		$html .= ' </ul><a href="javascript:void(0);" ectype="changeBrand" class="refresh-btn"><i class="iconfont icon-rotate-alt"></i><span>换一批</span></a></div>';
		$data['brands'] = $html;
		// dump($data);
		return json($data);
	}
}