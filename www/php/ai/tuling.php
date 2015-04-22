<?php

    function tuling($keyword){
        $apiKey = "dad8d3c02f6ca39257da3c89417b74e2"; 
        $apiURL = "http://www.tuling123.com/openapi/api?key=KEY&info=INFO";
        $req_method = 0;

        // 设置报文头, 构建请求报文 
        header("Content-type: text/html; charset=utf-8"); 
        $url = str_replace("INFO", $keyword, str_replace("KEY", $apiKey, $apiURL)); 

        if(req_method == 1){
            /** 方法一、用file_get_contents 以get方式获取内容 */ 
            $res =file_get_contents($url); 
            return $res; 
        } else {
            /** 方法二、使用curl库，需要查看php.ini是否已经打开了curl扩展 */ 
            $ch = curl_init(); 
            $timeout = 5; curl_setopt ($ch, CURLOPT_URL, $url); curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
            $file_contents = curl_exec($ch); 
            curl_close($ch);
            return $file_contents;
        }
    }
?>