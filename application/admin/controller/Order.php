<?php
namespace app\admin\controller;
use think\Db;
use PHPExcel;
class Order extends BaseController
{
    /* 遇到问题：1、订单筛选时，需要get到控制器不同参数当做where的条件，用tp内置的array()传参控制器这里
     *   接收不到参数，所以改用了原生的方式传参。
     *           2、上个问题遗留出的问题，用原生的方式传参，导致筛选出来的订单分页出了问题，分页时，参数
     *           并没有跟随，  解决: paginate(10,false,['query' => request()->param()]);
     *             加分页函数后的第三个参数(获取当前参数继续往下传递),具体内容手册
     */                            
	//显示订单
    public function lst()
    {   
        //订单查询
        if(request()->isPost()){
            $data = input('post.');
            $where = array();
            $selectValue = trim($data['select_value']); 
            if($data['select_base'] == 'out_trade_no'){
                $where['out_trade_no'] = ['=',$selectValue];
            }else{
                $userId = Db::name('user')->where('username',$selectValue)->value('id');
                $where['user_id'] = ['=',$userId];
            }
        }
        //订单导出 默认状态  逻辑：向列表页发送当前的status值，传给exportOrders参数
        $status = "all";  //默认导出所有订单
        // 订单筛选
        //paied(已支付) no_paied(未支付) posted(已发货) no_posted(未发货) receive(收货)
        $getData = input('get.');
        if(isset($getData['status'])){
            $status = $getData['status']; //订单筛选的状态值
            $where = array();
            if($getData['status'] == 'paied'){
                $where['pay_status'] = ['=','1'];
            }elseif($getData['status'] == 'no_paied'){
                $where['pay_status'] = ['=','0'];
            }elseif($getData['status'] == 'posted'){
                $where['post_status'] = ['=','1'];
            }elseif($getData['status'] == 'no_posted'){
                $where['post_status'] = ['=','0'];
            }elseif($getData['status'] == 'receive'){
                $where['post_status'] = ['=','2'];
            }
        }

        //不是查询订单需要给where赋值，防止报错
        if(!isset($where)){
            $where = 1;
        }
        //显示订单
    	$orderRes = Db::name('order')->alias('o')->join('user u','u.id = o.user_id')->field('o.*,u.username')->where($where)->order('o.id DESC')->paginate(10,false,['query' => request()->param()]);
        // halt($orderRes);
    	$this->assign([
            'orderRes' => $orderRes,
            'orderStatus'   => $status,
            ]);
        return view();
    }

    //显示订单详情
    public function detail($id){
        $orderInfo = Db::name('order')->alias('o')->join('user u','u.id = o.user_id')->field('o.*,u.username')->find($id);
        $this->assign('orderInfo',$orderInfo);
        return view();
    }

    //显示商品详情   （订单id  order_id）
    public function orderGoods($id){
        $orderGoods = Db::name('order_goods')->where('order_id',$id)->select();
        // dump($orderGoods);die();
        $this->assign([
            'orderGoods' => $orderGoods,
            ]);
        return view();
    }
    //修改订单内商品
    public function orderGoodsEdit($id){
        if(request()->isPost()){
            $data = input('post.');
            if(Db::name('order_goods')->update($data)){
                return 1;
            }else{
                return 0;
            }
        }   
        $orderGoodsInfo = Db::name('order_goods')->find($id);
        $this->assign([
            'orderGoodsInfo' => $orderGoodsInfo,
            ]);
        return view();
    }
    //删除订单内商品
    public function orderGoodsDel($id){
        $res = Db::name('order_goods')->delete($id);
        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    //修改订单
    public function edit()
    {
    	if(request()->isPost()){
    		$data = input('post.');
            // halt($data);
    		if(Db::name('order')->update($data) !== false){
    			return 1;
    		}else{
    			return 0;
    		}
    	}
    	//赋值
    	$id = input('id');
    	$orderInfo = Db::name('order')->alias('o')->join('user u','u.id = o.user_id')->field('o.*,u.username')->find($id);
    	$this->assign('orderInfo',$orderInfo);
        return view();
    }

    
    //删除订单
    public function del($id)
    {
        $orderGoods = Db::name('order_goods')->where('order_id',$id)->select();
        if($orderGoods){
            Db::name('order_goods')->where('order_id',$id)->delete();
        }
        $res = Db::name('order')->delete($id);
    	if($res){
    		return 1;
    	}else{
    		return 0;
    	}
    }

    //查询订单
    public function orderSelect(){

        return view();
    }

    //导出订单
    public function exportOrders(){
        $phpexcel = new PHPExcel();
        $phpexcel->setActiveSheetIndex(0); //选中生成的Excel的第一张工作表
        $sheet = $phpexcel->getActiveSheet();  //获取到选中的工作表
        //接收传来的参数  设置查询时的 where 条件
        $getData = input('get.');
        if(isset($getData['status'])){
            $where = array();
            if($getData['status'] == 'paied'){
                $where['pay_status'] = ['=','1'];
            }elseif($getData['status'] == 'no_paied'){
                $where['pay_status'] = ['=','0'];
            }
            elseif($getData['status'] == 'posted'){
                $where['post_status'] = ['=','1'];
            }
            elseif($getData['status'] == 'no_posted'){
                $where['post_status'] = ['=','0'];
            }
            elseif($getData['status'] == 'receive'){
                $where['post_status'] = ['=','2'];
            }
            else{
                $where['post_status'] = ['>','-1'];
            }
        }
        if(!isset($where)){
            $where = 1;
        }
        $orderRes = Db::name('order')->alias('o')->join('user u','o.user_id = u.id')
            ->field('o.*,u.username')->where($where)->order('o.id desc')->select();
        //设置表格的第一行标题
        $arr = [
            'id'                => '订单ID',
            'out_trade_no'      => '订单号',
            'username'          => '用户名',
            'goods_total_price' => '商品总价',
            'post_spent'        => '运费',
            'order_total_price' => '订单总价',
            'pay_status'        => '支付状态',
            'order_status'      => '订单状态',
            'post_status'       => '配送状态',
            'distribution'      => '配送方式',
            'payment'           => '支付方式',
            'name'              => '收货人姓名',
            'mobile_phone'      => '收货人电话',
            'address'           => '详细地址',
            'order_time'        => '下单时间'
        ];
        //订单数据前 加入$arr数组 当做标题
        array_unshift($orderRes,$arr);   
        $row = 0;   //声明行数，从第一行开始逐条插入
        // dump($orderRes);die();
        foreach ($orderRes as $k => $v) {
            $row += 1;
            // 第一次循环数组为表格标题，不需要做判断
            if($k != 0){
                if($v['pay_status'] == 0){
                    $v['pay_status'] = '未支付';
                }elseif($v['pay_status'] == 1){
                    $v['pay_status'] = '已支付';
                }

                if($v['order_status'] == 0){
                    $v['order_status'] = '未确认';
                }elseif($v['order_status'] == 1){
                    $v['order_status'] = '已确认';
                }

                if($v['post_status'] == 0){
                    $v['post_status'] = '未发货';
                }elseif($v['post_status'] == 1){
                    $v['post_status'] = '已发货';
                }elseif($v['post_status'] == 2){
                    $v['post_status'] = '已签收';
                }

                if($v['payment'] == 1){
                    $v['payment'] = '支付宝';
                }elseif($v['payment'] == 2){
                    $v['payment'] = '微信';
                }
                $v['order_time'] = date("Y-m-d H:i",$v['order_time']);
            }
            //格式:第一个参数字母代表 列 ，第二个 行，第三个代表要插入的数据   
            $sheet->setCellValue('A'.$row,$v['id'])  
                  ->setCellValue('B'.$row,$v['out_trade_no'])
                  ->setCellValue('C'.$row,$v['username'])
                  ->setCellValue('D'.$row,$v['goods_total_price'].'元')
                  ->setCellValue('E'.$row,$v['post_spent'].'元')
                  ->setCellValue('F'.$row,$v['order_total_price'].'元')
                  ->setCellValue('G'.$row,$v['pay_status'])
                  ->setCellValue('H'.$row,$v['order_status'])
                  ->setCellValue('I'.$row,$v['post_status'])
                  ->setCellValue('J'.$row,$v['distribution'])
                  ->setCellValue('K'.$row,$v['payment'])
                  ->setCellValue('L'.$row,$v['name'])
                  ->setCellValue('M'.$row,$v['mobile_phone'])
                  ->setCellValue('N'.$row,$v['address'])
                  ->setCellValue('O'.$row,$v['order_time']);
        }
        header('Content-Type: application/vnd.ms-excel');  //设置下载前的头信息 
        header('Content-Disposition: attachment;filename="订单表.xlsx"'); //设置文件名
        header('Cache-Control: max-age=0');
        $phpwriter = new \PHPExcel_Writer_Excel2007($phpexcel);  //写入表格 2007版本
        $phpwriter->save('php://output');    //文件通过浏览器下载

        /*
         *  以上下载方法为浏览器弹出下载提示框 下载
         *  方法二： 生成文件的方式，该方式不会弹出下载框，静默下载，
         *  例： $phpwriter->save('test.xls'); 
         */
        
        /*
            遇到的问题： 导出表格在Chrome内，浏览器直接无响应  
                     应该是浏览器的兼容性问题
                        在360急速浏览器无问题 （火狐未测试）
         */

    }


}
