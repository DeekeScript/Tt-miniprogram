<?php
/**
 * Created by PhpStorm.
 * User: 江桥
 * Date: 2019/4/3
 * Time: 17:05
 */
namespace TtMiniProgram\payment;

use TtMiniProgram\http;

class paymentClient{
    public $merchant_id;//商户号
    public $method='tp.trade.create';
    public $format='JSON';
    public $charset='utf-8';
    public $sign_type='MD5';
    public $sign;
    //public $timestamp;
    public $version='1.0';
    public $biz_content;
    public $currency='CNY';//币种  人民币
    public $notify_url;//异步回调地址
    public $risk_info;//用户的真实ip
    public $pay_discount=0;//折扣
    public $valid_time=15;//订单有效时间
    //public $trade_time;//下单时间戳
    public $out_order_no;//订单号
    public $uid;//用户id
    public $total_amount;//金额
    public $subject;
    public $body;
    public $ext_param;
    public $uid_type;
    public $service_fee=0;
    
    public function __construct($out_order_no,$uid,$total_amount,$subject,$body,$ext_param)
    {
        $this->out_order_no=$out_order_no;
        $this->uid=$uid;
        $this->total_amount=$total_amount;
        $this->subject=$subject;
        $this->body=$body;
        $this->ext_param=$ext_param;
    }
    
    public function orderRequest(){
        $url='https://tp-pay.snssdk.com/gateway';
        $data=[
            'app_id'=>$this->app_id,
            'method'=>$this->method,
            'format'=>$this->format,
            'charset'=>$this->charset,
            'sign_type'=>$this->sign_type,
            'timestamp'=>time(),
            'version'=>$this->version,
            'biz_content'=>$this->biz_content,
            'out_order_no'=>$this->out_order_no,
            'uid'=>$this->uid,
            'uid_type'=>$this->uid_type,
            'merchant_id'=>$this->merchant_id,
            'total_amount'=>$this->total_amount,
            'currency'=>$this->currency,
            'subject'=>$this->subject,
            'body'=>$this->body,
            'trade_time'=>time(),
            'valid_time'=>$this->valid_time,
            'notify_url'=>$this->notify_url,
            'service_fee'=>$this->service_fee,
            'risk_info'=>$this->risk_info,
            'ext_param'=>$this->ext_param,
        ];
        if($this->pay_discount){
            $data['pay_discount']=$this->pay_discount;
        }
        $data['sign']=sign($data);
        $client=new Client();
        $request=new Request('POST',$url,[],$data);
        try{
            $result=$client->send($request);
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    
    public function sign($data){
        sort($data);
        foreach($data as $k=>$v){
            if(is_array($v)){
                $data[$k]=json_encode($v);
            }
        }
        return md5(http_build_query($data));
    }
    
    public function setServiceFee($service_fee){
        $this->service_fee=$service_fee;
        return $this;
    }
    
    public function setNotifyUrl($notify_url){
        $this->notify_url=$notify_url;
        return $this;
    }
    
    public function setRiskInfo($risk_info){
        $this->risk_info=$risk_info;
        return $this;
    }
    
    public function setPayDiscount($pay_discount){
        $this->pay_discount=$pay_discount;
        return $this;
    }
    
    public function setValidTime($valid_time){
        $this->valid_time=$valid_time;
        return $this;
    }
}