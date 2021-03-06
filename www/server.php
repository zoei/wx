<?php
/**
 * 微信公众平台 PHP SDK 示例文件
 *
 * @author 
 */

  require('php/lib/Wechat.php');
  require('php/lib/dp-api.php');
  include_once "php/ai/tuling.php";
  include_once "php/util/log.php";

  $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
  Logger::log(time().": =======================\r\n".$postStr."\r\n-------------------------\r\n");

  /**
   * 微信公众平台演示类
   */
  class MyWechat extends Wechat {

    private $apiTool ;

    public function setApiTool($apiUrl, $appKey, $appSecret, $defaultParams)
    {
      $this->apiTool = new ApiTool($apiUrl, $appKey, $appSecret, $defaultParams);
    }


    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe() {
      $this->responseText('欢迎关注HappyMuslim!'.PHP_EOL.'请输入关键字，或者点击‘+’发送地理位置，获取团购');
    }

    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe() {
      $this->responseText('欢迎关注HappyMuslim!'.PHP_EOL.'请输入关键字，或者点击‘+’发送地理位置，获取团购');
    }

    /**
     * 收到文本消息时触发，使用发送的关键字进行团购搜索
     *
     * @return void
     */
    protected function onText() {
      $keyword = $this->getRequest('content');
      $params = array('keyword' => $keyword );
      $tool = $this->apiTool;
      $resultData = $tool->requestApi($params);
      Logger::log('reply: <------ '.$resultData." -------->\r\n");
      $this->sendResponse($resultData);
    }

    /**
     * 收到图片消息时触发，暂不支持
     *
     * @return void
     */
    protected function onImage() {
      $this->responseText('已收到您发送的图片');
    }

    /**
     * 收到地理位置消息时触发，进行附近团购搜索
     *
     * @return void
     */
    protected function onLocation() {
      $lat = $this->getRequest('location_x');
      $lng = $this->getRequest('location_y');
      $params = array('latitude' => $lat, 'longitude'=> $lng);
      $tool = $this->apiTool;
      $resultData = $tool->requestApi($params);
      $this->sendResponse($resultData);    
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink() {
      $this->responseText('已收到您发送的链接');
    }

    /**
     * 收到语音消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onVoice() {
      $keyword = $this->getRequest('recognition');
      
      // $params = array('keyword' => $keyword );
      // $result = tuling($keyword);
      // Logger::log('reply: <------ '.$result." -------->\r\n");
      // $this->responseText($result ? $result : ' ');

      $params = array('keyword' => $keyword );
      $tool = $this->apiTool;
      $resultData = $tool->requestApi($params);
      Logger::log('reply: <------ '.$resultData." -------->\r\n");
      $this->sendResponse($resultData);
    }

    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    protected function onUnknown() {
      $this->responseText('收到了未知类型消息：' . $this->getRequest('msgtype'));
    }

    /**
     * 回复团购消息，具体字段可以自行定制
     *
     * @return void
     */
    public function sendResponse($resultData){
       $status = $resultData['status'];
        if("OK" == $status){
        $items = array();
        array_push($items,new NewsResponseItem('以下数据由大众点评提供' , '以下数据由大众点评提供' , "http://j1.s2.dpfile.com/s/i/app/api/images/dp-logo.0905884f1e06baa7ac6fc7770c319f32.png", "http://happymuslim.net/wx/php/jssdk/sdktest.php"));
       
        $count = $resultData['count'];
        $deals = $resultData['deals'];
        foreach ($deals as $deal) {
          $title = $deal['title'].PHP_EOL.'仅售'.$deal['list_price'].'元';
          $distance = $deal['distance'];
          if($distance > 0){
             $title.=PHP_EOL.'距离'.$distance.'米';
          }
          $desc = $deal['description'];
          $picUrl = $deal['image_url'];
          $h5Url = $deal['deal_h5_url'];
          $newsItem = new NewsResponseItem($title , $desc , $picUrl, $h5Url);
          array_push($items, $newsItem);
        }
       $this->responseNews($items);
      }else{
        $this->responseText('Bad response for：'.$resultData['error']);
      }
    }
  }

  /**
   * Weixin Token为weixin，请自行设置，这里默认搜索上海的本地团购，其他城市请自行配置
   */
  $wechat = new MyWechat('happymuslim', TRUE);
  $wechat->setApiTool('http://api.dianping.com/v1/deal/find_deals', '86484869', '0c7c60d99eca4f7a9c02b1dcec69367c', array('format'=>'json','city'=>'上海','limit'=>'5','is_local'=>'1'));
  $wechat->run();
?>