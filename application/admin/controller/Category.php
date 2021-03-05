<?php
namespace app\admin\controller;
use think\Db;
use think\Validate;
use catetree\Catetree;
class Category extends BaseController
{
    //显示分类
    public function lst(){

        $category    = new Catetree();
        $categoryObj = db('category');
        if(request()->isPost()){
            $data = input('post.');
            $category->cateSort($data['sort'],$categoryObj);
        }
        $categoryRes = $categoryObj->order('sort ASC')->select();
        $categoryRes =  $category->Catetree($categoryRes);

        $_cateRec = Db::name('rec_item')->field('value_id,recpos_id')->where('value_type','2')->select();
        $cateRec = array();
        foreach ($_cateRec as $k => $v) {
            $cateRec[] = $v['value_id'];
        }
        // halt($cateRec);
        // halt($categoryRes);
        $this->assign([
            'categoryRes' => $categoryRes,
            'cateRec'     => $cateRec,
            ]);
        return view();
    }

    //添加分类
    public function add(){
        $category  = new Catetree();
        $categoryObj  = Db::name('category');
        if(request()->isPost()){
            $data = input('post.');
            halt($data);
            // //验证
            // $validate = validate('category');
            // if(!$validate->check($data)){
            //     $this->error($validate->getError());
            // }
            $add = model('category')->save($data);
            if($add){
                $this->success('添加分类成功','category/lst');
            }else{
                $this->error('添加分类失败！');
            }
            return;
        }
        //获取无限极分类
        $categoryRes = $categoryObj->select();
        $categoryRes =  $category->Catetree($categoryRes);
        //显示推荐位
        $categoryRecRes = Db::name('rec_pos')->where('rec_type','=',2)->select();
        // halt($categoryRecRes);
        $this->assign([
            'categoryRes' => $categoryRes,
            'categoryRecRes'=>$categoryRecRes,
            ]);
        return view();
    }

    //修改分类
    public function edit(){
        $category    = new Catetree();
        $categoryObj = db('category');
        if(request()->isPost()){
            $data = input('post.');
            // //验证
            // $validate = validate('category');
            // if(!$validate->check($data)){
            //     $this->error($validate->getError());
            // }
            $save = model('category')->update($data);
            if($save){
                $this->success('修改分类成功','category/lst');
            }else{
                $this->error('修改分类失败！');
            }
            return;
        }
        $id = input('id');
        $categorys   = $categoryObj->find($id);
        //获取无限极分类
        $categoryRes = $categoryObj->select();    
        $categoryRes =  $category->Catetree($categoryRes);
        //获取推荐位
        $categoryRecRes = Db::name('rec_pos')->where('rec_type','=',2)->select();
        //获取当前分类推荐位
        $curRecRes = Db::name('rec_item')->where(array('value_type'=>2,'value_id'=>$id))->select();
        $this->assign([
            'categoryRes'   => $categoryRes,
            'categorys'     => $categorys,
            'categoryRecRes'=> $categoryRecRes,
            'curRecRes'     => $curRecRes,
            ]);
        return view();
    }

    //删除分类
    public function del($id){
        //删除同时把子分类删除
        $category = Db::name('category');
        $Catetree = new Catetree();
        $sonids   = $Catetree->childrenids($id,$category);
        $sonids[] = intval($id);
        //同时删除分类推荐位
        foreach ($sonids as $k => $v) {
         Db::name('rec_item')->where(array('value_type'=>2,'value_id'=>$v))->delete();
        }

        // //删除分类前一同删除该分类下的文章和图片
        // $article = db('article');
        // foreach ($sonids as $k => $v) {
        //     $artRes = $article->field('id,thumb')->where(array('category_id'=>$v))->select();
        //     foreach ($artRes as $k1 => $v1) {
        //         $thumbSrc = IMG_UPLOADS.$v1['thumb'];
        //         if(file_exists($thumbSrc)){
        //             @unlink($thumbSrc);
        //         }
        //         $article->delete($v1['id']);
        //     }
        // }
        if($category->delete($sonids)){
            $this->success('分类删除成功!','category/lst');
        }
    }
    
}
