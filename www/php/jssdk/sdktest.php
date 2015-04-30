<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wx49270328cfa9d181", "89537bb6bd35fd48e6f3ac27cd68d68d");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type"> 
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="format-detection" content="telephone=no">
  <meta name="format-detection" content="email=no">
  <title>JS SDK Test</title>
  <style type="text/css">  
    body, html, #l-map {width: 100%;height: 100%;overflow: hidden;margin:0;}
    #l-map {height: 50%;}
  </style>
</head>
<body>
  <div id="l-map"></div>
  <div id="ready-state">loading</div>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=daefa8261b0b4fcf9ee9e05cff6b1994"></script>
<script>
  wx.config({
    debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'startRecord',
        'stopRecord',
        'onVoiceRecordEnd',
        'playVoice', 
        'pauseVoice',
        'stopVoice',
        'onVoicePlayEnd',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'translateVoice',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard'
    ]
  });
    function getLocation(cb){
        wx.getLocation({
            success: function (res) {
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                var speed = res.speed; // 速度，以米/每秒计
                var accuracy = res.accuracy; // 位置精度
                if(cb){
                    cb(longitude, latitude)
                }
            }
        });
    }
    function initMap(lon, lat){
        var position=new AMap.LngLat(lat, lon);
        var mapObj=new AMap.Map("l-map",{
                view: new AMap.View2D({//创建地图二维视口
                center:position,//创建中心点坐标
                zoom:14, //设置地图缩放级别
                rotation:0 //设置地图旋转角度
            }),
            lang:"zh_cn"//设置地图语言类型，默认：中文简体
        });//创建地图实例
    }
    wx.ready(function () {
        document.querySelector('#ready-state').innerText = 'ready';
        getLocation(initMap);
    });
</script>

</html>
