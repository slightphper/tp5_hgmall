<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Goods extends Model
{
    protected $field = true;

	protected static function init(){
        //添加前 主图及三张缩略图  商品编号
        Goods::beforeInsert(function ($goods){
            if($_FILES['og_thumb']['tmp_name']){
                $thumbName = $goods->upload('og_thumb');
                $ogThumb   = date("Ymd").DS.$thumbName;
                $bigThumb  = date("Ymd").DS.'big_'.$thumbName;
                $midThumb  = date("Ymd").DS.'mid_'.$thumbName;
                $smThumb   = date("Ymd").DS.'sm_'.$thumbName;
                //生成大小缩略图
                $image = \think\Image::open(GOODS_IMG_UPLOADS.$ogThumb);
                $image->thumb(config('big_thumb_width'),config('big_thumb_height'))->save(GOODS_IMG_UPLOADS.$bigThumb);
                $image->thumb(config('mid_thumb_width'),config('mid_thumb_height'))->save(GOODS_IMG_UPLOADS.$midThumb);
                $image->thumb(config('sm_thumb_width'),config('sm_thumb_height'))->save(GOODS_IMG_UPLOADS.$smThumb);
                //保存
                $goods->og_thumb = $ogThumb;
                $goods->big_thumb = $bigThumb;
                $goods->mid_thumb = $midThumb;
                $goods->sm_thumb = $smThumb;
            }
            //商品编号
            $goods->goods_code = time().rand(111111,999999);
        });

        //设置后置事件保存商品 会员价格  属性  相册
        Goods::afterInsert(function($goods){
            //会员价格处理
            $mpriceAttr = $goods->mp;
            $goodsId = $goods->id;
            if($mpriceAttr){
                foreach ($mpriceAttr as $k => $v) {
                    if(trim($v) == ''){
                        continue;
                    }else{
                        Db::name('member_price')->insert(['mlevel_id'=>$k,'mprice'=>$v,'goods_id'=>$goodsId]);
                    }
                }
            }   

            //处理商品属性
            // $goodsAttr = $goods->goods_attr;
            // $goodsPrice = $goods->goods_price;
            $goodsData = input('post.');
            $i = 0;
            if(isset($goodsData['goods_attr'])){
                foreach ($goodsData['goods_attr'] as $k => $v) {
                    if(is_array($v)){
                        if(!empty($v)){
                        //单选属性
                        foreach ($v as $k1 => $v1) {
                            if(!$v1){
                                $i++;
                                continue;
                            }
                            Db::name('goods_attr')->insert(['goods_id'=>$goodsId,'attr_id'=>$k,'attr_value'=>$v1,'attr_price'=>$goodsData['attr_price'][$i]]);
                            $i++;
                            }         
                        }           
                    }else{
                        //唯一属性
                        Db::name('goods_attr')->insert(['goods_id'=>$goodsId,'attr_id'=>$k,'attr_value'=>$v]);
                    }
                }
            }

            //商品推荐位处理
            if(isset($goodsData['rec_pos'])){
                foreach ($goodsData['rec_pos'] as $k => $v) {
                    Db::name('rec_item')->insert(['recpos_id'=>$v,'value_id'=>$goodsId,'value_type'=>1]);
                }
            }
                        
            //商品相册处理
            if($goods->_hasImgs($_FILES['goods_photo']['tmp_name'])){
                 //获取表单上传文件
                 $files = request()->file('goods_photo');
                 //移动到框架根目录/public/uploads/ 下
                   foreach($files as $file){
                        $info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'uploads'.DS.'goods_photo');
                        if($info){
                            //上传成功
                            $PhotoName = $info->getFilename();
                            $ogPhoto   = date("Ymd").DS.$PhotoName;
                            $bigPhoto  = date("Ymd").DS.'big_'.$PhotoName;
                            $midPhoto  = date("Ymd").DS.'mid_'.$PhotoName;
                            $smPhoto   = date("Ymd").DS.'sm_'.$PhotoName;
                            //生成大小缩略图
                            $image = \think\Image::open(GOODS_PHOTO_UPLOADS.$ogPhoto);
                            $image->thumb(config('big_thumb_width'),config('big_thumb_height'))->save(GOODS_PHOTO_UPLOADS.$bigPhoto);
                            $image->thumb(config('mid_thumb_width'),config('mid_thumb_height'))->save(GOODS_PHOTO_UPLOADS.$midPhoto);
                            $image->thumb(config('sm_thumb_width'),config('sm_thumb_height'))->save(GOODS_PHOTO_UPLOADS.$smPhoto);
                            Db::name('goods_photo')->insert(['goods_id'=>$goodsId,'mid_photo'=>$midPhoto,'big_photo'=>$bigPhoto,'sm_photo'=>$smPhoto,'og_photo'=>$ogPhoto]);
                        }else{
                            echo $file->getError();
                        }
                    }
            }
        });

        //设置事件修改 上传主图及缩略图、推荐位、会员价格、商品属性、商品相册
        Goods::beforeUpdate(function($goods){
            if($_FILES['og_thumb']['tmp_name']){
                //如果存在就删除旧图片
                @unlink(GOODS_IMG_UPLOADS.$goods->og_thumb);
                @unlink(GOODS_IMG_UPLOADS.$goods->big_thumb);
                @unlink(GOODS_IMG_UPLOADS.$goods->mid_thumb);
                @unlink(GOODS_IMG_UPLOADS.$goods->sm_thumb);
                //上传新文件
                $thumbName = $goods->upload('og_thumb');
                $ogThumb   = date("Ymd").DS.$thumbName;
                $bigThumb  = date("Ymd").DS.'big_'.$thumbName;
                $midThumb  = date("Ymd").DS.'mid_'.$thumbName;
                $smThumb   = date("Ymd").DS.'sm_'.$thumbName;
                //生成大小缩略图
                $image = \think\Image::open(GOODS_IMG_UPLOADS.$ogThumb);
                $image->thumb(config('big_thumb_width'),config('big_thumb_height'))->save(GOODS_IMG_UPLOADS.$bigThumb);
                $image->thumb(config('mid_thumb_width'),config('mid_thumb_height'))->save(GOODS_IMG_UPLOADS.$midThumb);
                $image->thumb(config('sm_thumb_width'),config('sm_thumb_height'))->save(GOODS_IMG_UPLOADS.$smThumb);
                //保存
                $goods->og_thumb  = $ogThumb;
                $goods->big_thumb = $bigThumb;
                $goods->mid_thumb = $midThumb;
                $goods->sm_thumb  = $smThumb;
            }
            //处理会员价格
            $mpriceArr  = $goods->mp;
            $goodsId = $goods->id;
            //删除原来价格
            Db::name('member_price')->where('goods_id','=',$goodsId)->delete();
            //批量写入修改后价格
            if($mpriceArr){
                foreach ($mpriceArr as $k => $v) {
                    if(trim($v) == ''){
                        continue;
                    }else{
                        Db::name('member_price')->insert(['goods_id'=>$goodsId,'mlevel_id'=>$k,'mprice'=>$v]);
                    }
                }
            }
            $goodsData = input('post.');
            //商品推荐位处理
            Db::name('rec_item')->where(array('value_id'=>$goodsId,'value_type'=>1))->delete();
            if(isset($goodsData['rec_pos'])){
                foreach ($goodsData['rec_pos'] as $k => $v) {
                    Db::name('rec_item')->insert(['recpos_id'=>$v,'value_id'=>$goodsId,'value_type'=>1]);
                }
            }

            /*处理商品属性（修改属性内容）
             *实现原理：先接收传过来的参数，如果存在属性数组，先把当前商品中的属性删除，在重新存入goods_attr中
             * 
             */
            
            if(isset($goodsData['goods_attr'])){
                Db::name('goods_attr')->where('goods_id','=',$goodsId)->delete();
                $i = 0;
                foreach ($goodsData['goods_attr'] as $k => $v) {
                    if(is_array($v)){
                        if(!empty($v)){
                            foreach ($v as $k1 => $v1) {
                                if(!$v1){
                                    $i++;
                                    continue;
                                }else{
                                    Db::name('goods_attr')->insert(['goods_id'=>$goodsId,'attr_id'=>$k,'attr_value'=>$v1,'attr_price'=>$goodsData['attr_price'][$i]]);
                                    $i++;
                                }
                            }
                        }
                    }else{
                        //处理唯一属性
                        Db::name('goods_attr')->insert(['attr_id'=>$k,'attr_value'=>$v,'goods_id'=>$goodsId]);
                    }
                }
            }


            // //处理商品属性（修改属性内容）
            // $goodsData = input('post.');
            // if(isset($goodsData['goods_attr'])){
            //     $i = 0;
            //     foreach ($goodsData['goods_attr'] as $k => $v) {
            //         if(is_array($v)){
            //             if(!empty($v)){
            //                 foreach ($v as $k1 => $v1) {
            //                     if(!$v1){
            //                         $i++;
            //                         continue;
            //                     }else{
            //                         Db::name('goods_attr')->insert(['goods_id'=>$goodsId,'attr_id'=>$k,'attr_value'=>$v1,'attr_price'=>$goodsData['attr_price'][$i]]);
            //                         $i++;
            //                     }
            //                 }
            //             }
            //         }else{
            //             //处理唯一属性
            //             Db::name('goods_attr')->insert(['attr_id'=>$k,'attr_value'=>$v,'goods_id'=>$goodsId]);
            //         }
            //     }
            // }

            //商品相册处理
            if($goods->_hasImgs($_FILES['goods_photo']['tmp_name'])){
                 //获取表单上传文件
                 $files = request()->file('goods_photo');
                 //移动到框架根目录/public/static/uploads/goods_photo/ 下
                   foreach($files as $file){
                        $info = $file->move(GOODS_PHOTO_UPLOADS);
                        if($info){
                            //上传成功
                            $PhotoName = $info->getFilename();
                            $ogPhoto   = date("Ymd").DS.$PhotoName;
                            $bigPhoto  = date("Ymd").DS.'big_'.$PhotoName;
                            $midPhoto  = date("Ymd").DS.'mid_'.$PhotoName;
                            $smPhoto   = date("Ymd").DS.'sm_'.$PhotoName;
                            //生成大小缩略图
                            $image = \think\Image::open(GOODS_PHOTO_UPLOADS.$ogPhoto);
                            $image->thumb(config('big_thumb_width'),config('big_thumb_height'))->save(GOODS_PHOTO_UPLOADS.$bigPhoto);
                            $image->thumb(config('mid_thumb_width'),config('mid_thumb_height'))->save(GOODS_PHOTO_UPLOADS.$midPhoto);
                            $image->thumb(config('sm_thumb_width'),config('sm_thumb_height'))->save(GOODS_PHOTO_UPLOADS.$smPhoto);
                            Db::name('goods_photo')->insert(['goods_id'=>$goodsId,'mid_photo'=>$midPhoto,'big_photo'=>$bigPhoto,'sm_photo'=>$smPhoto,'og_photo'=>$ogPhoto]);
                        }else{
                            echo $file->getError();
                        }
                    }
                 }
      
        });

        //设置事件删除商品前的关联内容
        Goods::beforeDelete(function($goods){
            $goodsId = $goods->id;
            //删除主图及三张缩略图
            if($goods->og_thumb){
                $thumb = [];
                $thumb[] = GOODS_IMG_UPLOADS.$goods->og_thumb;
                $thumb[] = GOODS_IMG_UPLOADS.$goods->big_thumb;
                $thumb[] = GOODS_IMG_UPLOADS.$goods->mid_thumb;
                $thumb[] = GOODS_IMG_UPLOADS.$goods->sm_thumb;
                foreach ($thumb as $k => $v) {
                    if(file_exists($v)){
                        @unlink($v);
                    }
                }
            }
            //删除商品推荐位
            Db::name('rec_item')->where(array('value_type'=>1,'value_id'=>$goodsId))->delete();
            //删除关联会员价格
            Db::name('member_price')->where('goods_id','=',$goodsId)->delete();
            //删除关联商品属性
            Db::name('goods_attr')->where('goods_id','=',$goodsId)->delete();
            //删除库存量
            
            //删除关联商品相册
            $goodsPhotoRes = model('goods_photo')->where('goods_id','=',$goodsId)->select();
            if(!empty($goodsPhotoRes)){
                foreach ($goodsPhotoRes as $k => $v) {
                    if($v->og_photo){
                        $photo = [];
                        $photo[] = GOODS_PHOTO_UPLOADS.$v->og_photo;
                        $photo[] = GOODS_PHOTO_UPLOADS.$v->big_photo;
                        $photo[] = GOODS_PHOTO_UPLOADS.$v->mid_photo;
                        $photo[] = GOODS_PHOTO_UPLOADS.$v->sm_photo;
                        foreach ($photo as $k1 => $v1) {
                            if(file_exists($v1)){
                                @unlink($v1);
                            }
                        }
                    }
                }
            }
            Db::name('goods_photo')->where('goods_id','=',$goodsId)->delete();
        });
    }

    //判断商品相册是否上传
    private function _hasImgs($tmpArr){
        foreach ($tmpArr as $k => $v) {
             if($v){
                return true;
             }
        }
        return false;
    }
    
    
    //图片上传
    public function upload($imgName){
        //获取表单上传文件
        $file = request()->file($imgName);
        //移动到框架根目录/public/static/uploads/goods_thumb/ 下
        if($file){
            $info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'uploads'.DS.'goods_thumb');
            if($info){
                //上传成功
               return $info->getFilename();
            }else{
                echo $file->getError();
            }
        }
    }


}
