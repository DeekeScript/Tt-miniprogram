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

class ttClient
{
    public $app_id;
    public $secret;
    public $uid;

    public function __construct($app_id, $uid, $secret)
    {
        $this->uid = $uid;
        $this->app_id = $app_id;
        $this->secret = $secret;
    }

    public function getAccessToken()
    {
        $grant_type = 'client_credential';
        $url = 'https://developer.toutiao.com/api/apps/token';
        $data = [
            'app_id' => $this->app_id,
            'secret' => $this->secret,
            'grant_type' => $grant_type
        ];
        $client = new Client();
        $request = new Request('GET', $url . '?' . http_build_query($data), [], []);
        try {
            $result = $client->send($request, ['verify' => false]);
            return $result->getBody()->getContents();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function jscode2session($code, $anonymous_code = '')
    {
        $url = 'https://developer.toutiao.com/api/apps/jscode2session';
        $data = [
            'app_id' => $this->app_id,
            'secret' => $this->secret,
            'code' => $code,
        ];
        if ($anonymous_code) {
            $data['anonymous_code'] = $anonymous_code;
        }
        $client = new Client();
        $request = new Request('GET', $url . '?' . http_build_query($data), [], '');
        try {
            $result = $client->send($request, ['verify' => false]);
            return $result->getBody()->getContents();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function checkSignature($rawData, $session_key, $signature)
    {
        return sha1($rawData . $session_key) === $signature;
    }

    public function getUserInfo($encryptedData, $session_key, $iv)
    {
        $key = base64_decode($session_key);
        $iv = base64_decode($iv);
        return openssl_decrypt(base64_decode($encryptedData), 'aes-128-cbc', $key, true, $iv);
    }
}