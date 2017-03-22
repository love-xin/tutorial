<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wx48bb285530fa5958", "bffb1eb4ed5721eb61e9e2b17cdffc8e");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
  <title></title>
</head>
<body>
  <button id="startRecord">开始录音</button>
  <button id="stopRecord">停止录音</button>
  <button id="playVoice">播放录音</button>
  <button id="translateVoice">识别录音</button>
  <button id="getLocation">获取定位信息</button>
  <button id="scanQRCode">扫一扫</button>
  <button id="chooseImage">选择图片</button>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
  wx.config({
    debug: true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'startRecord',
      'stopRecord',
      'playVoice',
      'translateVoice',
      'getLocation',
      'openLocation',
      'scanQRCode',
      'chooseImage'
    ]
  });
  wx.ready(function () {
    // 在这里调用 API
    wx.checkJsApi({
      jsApiList:['startRecord'],
      success:function(data){
        //alert(JSON.stringify(data))
      }
    })
    document.querySelector("#startRecord").onclick=function(){
      wx.startRecord();
    }
    var voice = {};
    document.querySelector("#stopRecord").onclick=function(){
    wx.stopRecord({
      success: function (res) {
          voice.localId = res.localId;
      }
      });
    }
    document.querySelector("#playVoice").onclick=function(){
    wx.playVoice({
      localId: voice.localId // 需要播放的音频的本地ID，由stopRecord接口获得
    });
    }
    document.querySelector("#translateVoice").onclick=function(){
    wx.translateVoice({
   localId: voice.localId, // 需要识别的音频的本地Id，由录音相关接口获得
    isShowProgressTips: 1, // 默认为1，显示进度提示
    success: function (res) {
        //alert(res.translateResult); // 语音识别的结果
        $.get("http://www.tuling123.com/openapi/api?key=c75ba576f50ddaa5fd2a87615d144ecf&info="+res.translateResult,function(data){
          alert(data.text);
          //alert(JSON.parse(data).text+"/////")
        })
    }
});
}

document.querySelector("#getLocation").onclick=function(){
wx.getLocation({
    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
    success: function (res) {
        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
        var speed = res.speed; // 速度，以米/每秒计
        var accuracy = res.accuracy; // 位置精度
        
        wx.openLocation({
          latitude: latitude, // 纬度，浮点数，范围为90 ~ -90
          longitude: longitude, // 经度，浮点数，范围为180 ~ -180。
          name: '', // 位置名
          address: '', // 地址详情说明
          scale: 28, // 地图缩放级别,整形值,范围从1~28。默认为最大
          infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
        });
    }
});
}
document.querySelector("#scanQRCode").onclick=function(){
wx.scanQRCode({
    needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
    scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
    success: function (res) {
    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
}
});
}
document.querySelector("#chooseImage").onclick=function(){
wx.chooseImage({
    count: 1, // 默认9
    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
    success: function (res) {
        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
    }
});
}
   
  });
</script>
</html>
