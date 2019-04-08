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

$m=new ttClient(132321321,123213213,21321321321321);
try{
    var_dump($m->jscode2session(213213213213));
}catch(\Exception $e){
    var_dump($e->getMessage());
}
