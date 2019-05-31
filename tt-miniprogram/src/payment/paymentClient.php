<?php
/**
 * Created by PhpStorm.
 * User: 江桥
 * Date: 2019/4/3
 * Time: 17:05
 */

namespace TtMiniProgram\payment;

use Curl\Curl;
use GuzzleHttp\Client;

class paymentClient
{
    const PUBLIC_KEY = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDOZZ7iAkS3oN970+yDONe5TPhPrLHoNOZOjJjackEtgbptdy4PYGBGdeAUAz75TO7YUGESCM+JbyOz1YzkMfKl2HwYdoePEe8qzfk5CPq6VAhYJjDFA/M+BAZ6gppWTjKnwMcHVK4l2qiepKmsw6bwf/kkLTV9l13r6Iq5U+vrmwIDAQAB';
    public $merchant_id;//商户号
    public $method = 'tp.trade.create';
    public $format = 'JSON';
    public $charset = 'utf-8';
    public $sign_type = 'MD5';
    public $sign;
    public $version = '1.0';
    public $biz_content;
    public $currency = 'CNY';//币种  人民币
    public $notify_url;//异步回调地址
    public $risk_info = ['ip' => '127.0.0.1', 'device_id' => '123456'];//用户的真实ip
    public $valid_time = '60';//订单有效时间
    public $out_order_no;//订单号
    public $uid;//用户id
    public $total_amount;//金额
    public $subject;
    public $body;
    public $secret;

    public function __construct($app_id, $mch_id, $secret, $out_order_no, $uid, $total_amount, $subject, $body)
    {
        $this->out_order_no = $out_order_no;
        $this->uid = $uid;
        $this->total_amount = $total_amount;
        $this->subject = $subject;
        $this->body = $body;
        $this->app_id = $app_id;
        $this->merchant_id = $mch_id;
        $this->secret = $secret;
    }

    public function orderRequest()
    {
        $time = time() . '';
        $url = 'https://tp-pay.snssdk.com/gateway';
        $data = [
            'app_id' => $this->app_id,
            'method' => $this->method,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => $time,
            'version' => $this->version,
            'biz_content' => [
                'out_order_no' => $this->out_order_no,
                'uid' => $this->uid,
                'merchant_id' => $this->merchant_id,
                'total_amount' => $this->total_amount,
                'currency' => $this->currency,
                'subject' => $this->subject,
                'body' => $this->body,
                'trade_time' => $time,
                'valid_time' => $this->valid_time,
                'notify_url' => $this->notify_url,
                'risk_info' => $this->risk_info,
            ],
        ];

        $data['sign'] = $this->sign($data);
        $client = new Client();
        try {
            $data['biz_content'] = json_encode($data['biz_content']);
            $result = $client->request('POST', $url, [
                'form_params' => $data,
                'headers' => [
                    'Content-Type' => "application/x-www-form-urlencoded"
                ]
            ]);
            return json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    //验证字节跳动返回的内容是否正确
    public function checkSign($data, $sign)
    {
        ksort($data);
        $signData = '';
        foreach ($data as $k => $v) {
            $signData .= '&' . $k . '=' . $v;
        }
        $signData = ltrim($signData, '&');
        $pem = "-----BEGIN PUBLIC KEY-----\n".chunk_split(self::PUBLIC_KEY,64,"\n")."-----END PUBLIC KEY-----";
        $pkeyid = openssl_pkey_get_public($pem);
        $sign = base64_decode($sign);
        if ($pkeyid) {
            $verify = openssl_verify($signData, $sign, $pkeyid, OPENSSL_ALGO_MD5);
            openssl_free_key($pkeyid);
        }
        return $verify;
    }

    public function sign($data)
    {
        $signData = '';
        ksort($data);
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $value = json_encode($v);
            } else {
                $value = $v;
            }
            if ($value) {
                $signData .= '&' . $k . '=' . $value;
            }
        }

        $datas = ltrim($signData, '&');
        return md5($datas . $this->secret);
    }

    public function setServiceFee($service_fee)
    {
        $this->service_fee = $service_fee;
        return $this;
    }

    public function setNotifyUrl($notify_url)
    {
        $this->notify_url = $notify_url;
        return $this;
    }

    function setIpAndDeviceId($ip, $device_id)
    {
        $this->risk_info = ['ip' => $ip, 'device_id' => $device_id];//用户的真实ip
    }

    public function setRiskInfo($risk_info)
    {
        $this->risk_info = $risk_info;
        return $this;
    }

    public function setPayDiscount($pay_discount)
    {
        $this->pay_discount = $pay_discount;
        return $this;
    }

    public function setValidTime($valid_time)
    {
        $this->valid_time = $valid_time;
        return $this;
    }
}