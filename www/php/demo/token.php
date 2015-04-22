<?php

	/**
	* 根据微信appID，appsecret 获取access_token 并缓存7200s
	* @param string $appID 微信appID
	* @param string $appsecret 微信appsecret
	* @param string $token 哪个用户的
	* @return string $access_token 返回公众号access_token
	*/
	function get_access_token($appID, $appsecret, $token){

		static $access_token;
		/* 获取缓存数据 */
		$access_token = S($token.'weixin_access_token');

		if($access_token) { //已缓存，直接使用
			return $access_token;
		} else { //获取access_token
			$url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appID.'&secret='.$appsecret;
			$ch1 = curl_init ();
			$timeout = 5;
			curl_setopt ( $ch1, CURLOPT_URL, $url_get );
			curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
			curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
			$accesstxt = curl_exec ( $ch1 );
			curl_close ( $ch1 );
			$access = json_decode ( $accesstxt, true );

			// 缓存数据7200秒
			S($token.'weixin_access_token', $access['access_token'], 7200);
			return $access['access_token'];
		}
	}
?>