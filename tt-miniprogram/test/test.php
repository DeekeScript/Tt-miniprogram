<?php
/**
 * Created by PhpStorm.
 * User: 江桥
 * Date: 2019/4/8
 * Time: 11:38
 */

namespace TtMiniProgram\test;
include "../../vendor/autoload.php";


use TtMiniProgram\http;
use TtMiniProgram\ttClient;

$m = new ttClient(132321321, 123213213, 21321321321321);
try {
    var_dump($m->jscode2session(213213213213));
} catch (\Exception $e) {
    var_dump($e->getMessage());
}

$m = openssl_encrypt("江桥在上网", 'aes-128-cbc', "12321321321", true, "1234567891234567");
echo $m."\r\n";

echo openssl_decrypt($m, 'aes-128-cbc', "12321321321", true, "1234567891234567");
