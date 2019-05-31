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
    public $appid;
    public $secret;

    public function __construct($appid, $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    public function getAccessToken()
    {
        $grant_type = 'client_credential';
        $url = 'https://developer.toutiao.com/api/apps/token';
        $data = [
            'appid' => $this->appid,
            'secret' => $this->secret,
            'grant_type' => $grant_type
        ];
        $client = new Client();
        $request = new Request('GET', $url . '?' . http_build_query($data), [], '');
        try {
            $result = $client->send($request, ['verify' => false]);
            $data = json_decode($result->getBody()->getContents(), true);
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function jscode2session($code, $anonymous_code = '')
    {
        $url = 'https://developer.toutiao.com/api/apps/jscode2session';
        $data = [
            'appid' => $this->appid,
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

    private function checkSignature($rawData, $session_key, $signature)
    {
        return sha1($rawData . $session_key) === $signature;
    }

    public function getUserInfo($encryptedData, $session_key, $iv, $rawData, $signature)
    {
        $key = base64_decode($session_key);
        $iv = base64_decode($iv);
        $plaintext = openssl_decrypt(base64_decode($encryptedData), 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        // trim pkcs#7 padding
        $pad = ord(substr($plaintext, -1));
        $pad = $pad < 1 || $pad > 32 ? 0 : $pad;
        $plaintext = substr($plaintext, 0, strlen($plaintext) - $pad);
        if ($this->checkSignature($rawData, $session_key, $signature)) {
            return $plaintext;
        }
        return false;
    }
}