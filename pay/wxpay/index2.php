<?php
include  $payPlus.'Base.php';
include $payPlus.'/phpqrcode/phpqrcode.php';


class WeiXinPay2 extends Base
{
    //1.调用统一下单API 后去二维码支付链接
    public function getQrUrl($outTradeNo,$orderTotalPrice){
        //调用统一下单API
           $params = [
                'appid'=> self::APPID,
                'mch_id'=> self::MCHID,
                'nonce_str'=>md5(time()),
                'body'=> '扫码支付模式二',     //支付标题
                'out_trade_no'=> $outTradeNo,  //订单编号
                'total_fee'=> $orderTotalPrice, //金额  按分换算
                // 'spbill_create_ip'=>$_SERVER['SERVER_ADDR'], //外网ip
                'spbill_create_ip'=>'127.0.0.1',
                'notify_url'=> self::NOTIFY,
                'trade_type'=>'NATIVE',
                'product_id'=>$outTradeNo
           ];
       $arr = $this->unifiedorder($params);
       return $arr['code_url'];
    }

}

// $obj = new WeiXinPay2();
// $qrurl = $obj->getQrUrl('1118');

//  //2.生成二维码
//  QRcode::png($qrurl);