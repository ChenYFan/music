<?php if(basename($_SERVER['PHP_SELF']) === basename(__FILE__)) exit;?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <title>帮助信息 - 全能VIP在线音乐解析 - 陈</title>
  <meta name="keywords" content="音乐解析,在线解析,QQ音乐解析,网易云音乐解析,网易云VIP解析,VIP音乐解析,免费下载,付费歌曲,收费歌曲,音乐解析网站,音乐解析下载,全能音乐解析">
  <meta name="description" content="">
  <link rel="stylesheet" href="https://cdnjs.loli.net/ajax/libs/amazeui/2.7.2/css/amazeui.flat.min.css">
  <link rel="stylesheet" href="../css/public.css">
  <meta http-equiv="Cache-Control" content="no-siteapp">
  <style>.title{font-size: 30px;margin: 20px 0 8px;}</style>
</head>
<body>
<?php $title='音乐解析';include '../header.php';?>
<div class="am-container">
	<div class="title" id="q1">解析示例</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">QQ音乐</div>
	  <div class="am-panel-bd">
		单曲链接：https://y.qq.com/n/yqq/song/000XNcaR17vzPm.html<br>
		专辑链接：https://y.qq.com/n/yqq/album/000ym9e23zZSBL.html<br>
		歌单链接：https://y.qq.com/n/yqq/playsquare/3846214337.html<br>
		歌手链接：https://y.qq.com/n/yqq/singer/002J4UUk29y8BY.html
	  </div>
	</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">网易云音乐</div>
	  <div class="am-panel-bd">
		单曲链接：https://music.163.com/#/song?id=1306507665<br>
		歌单链接：https://music.163.com/#/playlist?id=2415608589
	  </div>
	</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">全民K歌</div>
	  <div class="am-panel-bd">
		单曲链接：https://node.kg.qq.com/play?s=8DIbld8acyZTg80e
	  </div>
	</div>
	<div class="title" id="q2">常见问题</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">没有搜索到我要的歌曲？</div>
	  <div class="am-panel-bd">
		试试搜索 歌名 - 歌手
	  </div>
	</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">出现 An audio error has occurred, player will skip forward in 2 seconds.</div>
	  <div class="am-panel-bd">
		说明无法解析该歌曲
	  </div>
	</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">UC浏览器下载无损音质拒绝访问怎么办？</div>
	  <div class="am-panel-bd">
		方法一：更换浏览器<br>
		方法二：清除streamoc.music.tc.qq.com的cookie，清除后请不要访问QQ音乐。<br>
	  </div>
	</div>
	<div class="am-panel am-panel-default">
	  <div class="am-panel-hd">为什么下载的歌曲名字是乱码？</div>
	  <div class="am-panel-bd">
		由于浏览器跨域和服务器限制等原因，请手动复制歌名
	  </div>
	</div>
</div>
<?php include '../footer.php';?>
<?php include '../end.php';?>
</body>
</html>
