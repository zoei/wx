<?php
 
define("APPID", "wxb1e15dc25e06a5b5");
define("APPSECRET", "d005b1c83b7ef36f904dd2d25e4e9966");
 
$token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . APPID . "&secret=" . APPSECRET;
$res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
//echo $res;
$result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
$access_token = $result['access_token'];
echo $access_token;
 
?>