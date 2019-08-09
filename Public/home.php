<?php if(basename($_SERVER['PHP_SELF']) === basename(__FILE__)) exit;?>
<!DOCTYPE html>
<html>
<head>
  <?php include './head.php';?>
  <title>全能VIP在线音乐解析 - 陈 -内部私定版</title>
  <meta name="keywords" content="音乐解析,在线解析,QQ音乐解析,网易云音乐解析,网易云VIP解析,VIP音乐解析,免费下载,付费歌曲,收费歌曲,音乐解析网站,音乐解析下载,全能音乐解析">
  <meta name="description" content="本站提供QQ音乐网易云VIP解析服务，让你省去购买音乐VIP费用，请大家不要收藏本站，不要将它介绍给你的朋友！">
  <link rel="stylesheet" href="<?php echo SITE_URL;?>Public/css/main.css?v=<?php echo APP_VERSION;?>">
  <meta name="referrer" content="same-origin">
</head>
<body>
<div id="loader"><div id="loader-content"></div><div id="loader-text">陈-正在渲染页面<br>长时间停留此页面请刷新网页</div></div>

<div class="header">
	<div class="am-container">
		<a id="title">全能VIP在线音乐解析</a>
		<div class="am-fr" id="menu-group">
			<div class="am-dropdown" data-am-dropdown>
			  <a class="am-dropdown-toggle"><i class="am-icon-ellipsis-v"></i>菜单</a>
			  <ul class="am-dropdown-content">
					<li class="am-dropdown-header">o(〃'▽'〃)o</li>
					<li><a href="/"><i class="am-icon-home"></i> 返回首页</a></li>
					<li><a data-am-modal="{target: '#trophy'}"><i class="am-icon-trophy"></i> 排行榜</a></li>
					<li><a href="question.php#q1" target="_blank"><i class="am-icon-info"></i> 解析列表</a></li>
					<li><a href="question.php#q2" target="_blank"><i class="am-icon-question"></i> 常见问题</a></li>
					<li><a data-am-modal="{target: '#fenxiang'}"><i class="am-icon-share-alt"></i> 分享</a></li>
								  </ul>
			</div>
		</div>
		<div class="am-fr" id="fast-download">
			<a data-br="m4a">M4A</a>
			<a data-br="128">128K</a>
			<a data-br="320">320K</a>
			<a data-br="flac">FLAC</a>
			<a data-br="ape">APE</a>
			<a id="download"></a>
		</div>
	</div>
</div>

<div class="am-container" id="main">
	<form id="search">
		<div class="am-input-group am-input-group-secondary">
		<select data-am-selected id="type">
			<option value="qq">QQ</option>
			<option value="myfreemp3">MyFreeMp3</option>
			<option value="netease">网易云</option>
			<option value="kugou">酷狗</option>
			<option value="kuwo">酷我</option>
			<option value="xiami">虾米</option>
			<option value="baidu">百度</option>
			<option value="1ting">一听</option>
			<option value="migu">咪咕</option>
			<option value="lizhi">荔枝</option>
			<option value="qingting">蜻蜓</option>
			<option value="ximalaya">喜马拉雅</option>
			<option value="kg">全民K歌</option>
			<option value="5singyc">5sing原创</option>
			<option value="5singfc">5sing翻唱</option>
		</select>
			<input type="text" autocomplete="off" class="am-form-field am-field-valid" id="input" placeholder="歌名 - 歌手" pattern="^.+$" required>
			<span class="am-input-group-btn" data-am-dropdown="">
				<button class="am-btn am-btn-secondary" id="empty" type="button"><span class="am-icon-remove"></span></button>
				<button class="am-btn am-btn-secondary" type="submit"><span class="am-icon-search"></span></button>
			</span>
		</div>
		<div class="smartbox">
			<a id="close">关闭</a>
			<a class="smartbox_group">
				单曲
			</a>
			<a class="smartbox_group">
				歌手
			</a>
		</div>
	</form>
	<div id="homePage" class="row">
		<div class="am-g">
			<div class="home-title">公告</div>
			<div id="msg" style="color: red;">请手动按Ctrl+F5刷新网页</div>
			<div class="home-title">歌单推荐</div>
			<div id="recomPlaylist"></div>
			<div class="home-title">热门搜索</div>
			<div id="hotkey" class="key-group"></div>
		</div>
	</div>
	<div id="audioPage" class="row">
		<div id="player" class="aplayer"></div>
	</div>
	<div id="downloadPage" class="row">
		<div class="am-g">
			<div class="am-u-lg">
				链接仅提供下载服务，请勿用作外链。<br>
				安卓端用户请使用360极速浏览器。<br>
				PC端用户请使用Chrome或QQ浏览器。<br>
				下载速度取决于你的网速和配置。<br>

			</div>
		</div>
	</div>
	<div id="floatbtn">
		<button class="am-btn" id="go-top">回到顶部</button>
		<button class="am-btn" id="zipdownload">批量下载</button>
		<a href="https://greasyfork.org/zh-CN/scripts/370308" target="_blank" rel="nofollow"><button class="am-btn">油猴脚本</button></a>
	</div>
</div>
<footer>
	<div class="colour-border"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
	<div class="am-container">
	本站本身不储存任何资源文件，资源来自互联网，仅供学习交流试听，禁用于任何商业用途或公开传播的场合，版权归唱片公司所有，请于下载后24小时内删除，支持购买正版专辑！<br>
	<br>
	© 2015 - 2019 陈 &原<a href="http://tools.liumingye.cn/music">刘明野的工具箱</a>所有
	</div>
</footer>
<!-- 不显示的元素 -->
<div class="am-modal" tabindex="-1" id="qrcode">
  <div class="am-modal-dialog">
	<div class="am-modal-hd">扫描二维码下载</div>
	<div class="am-modal-bd"></div>
	<div class="am-modal-footer"> <span class="am-modal-btn">确定</span>
	</div>
  </div>
</div>
<div class="am-modal" tabindex="-1" id="batch">
  <div class="am-modal-dialog">
	<div class="am-modal-hd">批量打包下载</div>
	<div class="am-modal-bd"></div>
	<div class="am-modal-footer">
		<span class="am-modal-btn">优先无损下载</span>
		<span class="am-modal-btn">优先高品下载</span>
	</div>
  </div>
</div>
<div class="am-modal" tabindex="-1" id="trophy">
  <div class="am-modal-dialog">
	<div class="am-modal-bd"></div>
	<div class="am-modal-footer">
		<span class="am-modal-btn">关闭</span>
	</div>
  </div>
</div>
<script src="<?php echo SITE_URL;?>Public/js/localstorage.js?v=<?php echo APP_VERSION;?>"></script>
<!--
</body>
</html>
