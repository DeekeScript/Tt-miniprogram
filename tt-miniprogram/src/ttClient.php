<?php

/**
 * Created by PhpStorm.
 * User: 江桥
 * Date: 2019/4/3
 * Time: 16:34
 */

namespace TtMiniProgram;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class ttClient{
    public $app_id;
    public $secret;
    public $uid;
    
    public function __construct($app_id,$uid,$secret)
    {
        $this->uid=$uid;
        $this->app_id=$app_id;
        $this->secret=$secret;
    }
    
    public function getAccessToken(){
        $grant_type='client_credential';
        $url='https://developer.toutiao.com/api/apps/token';
        $data=[
            'app_id'=>$this->app_id,
            'secret'=>$this->secret,
            'grant_type'=>$grant_type
        ];
        $client=new Client();
        $request=new Request('GET',$url.'?'.http_build_query($data),[],[]);
        try{
            $result=$client->send($request);
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    
    public function jscode2session($code,$anonymous_code=''){
        $url='https://developer.toutiao.com/api/apps/jscode2session';
        $data=[
            'app_id'=>$this->app_id,
            'secret'=>$this->secret,
            'code'=>$code,
        ];
        if($anonymous_code){
            $data['anonymous_code']=$anonymous_code;
        }
        $client=new Client();
        $request=new Request('GET',$url.'?'.http_build_query($data),[],'');
        try{
            $result=$client->send($request);
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}