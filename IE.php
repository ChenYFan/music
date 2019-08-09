
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8"/>
<meta name="renderer" content="ie-stand"/>
<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>请升级你的浏览器 - 陈</title>
<meta name="description" content="这是一个开放的旧版IE升级提示页，普通用户可通过本页介绍快速了解为何要升级旧版IE浏览器以及如何升级浏览器，开发者可引用本页提供的代码为网站接入旧版IE升级提示。"/>
<link rel="icon" type="image/x-icon" href="/favicon.ico"/>
<meta http-equiv="Cache-Control" content="no-siteapp"/>
<meta http-equiv="Cache-Control" content="no-transform"/> 
<link type="text/css" rel="stylesheet" href="css/style.css"/>
<script>
    var l = window.location, s = l.search.substr(1),
        r = '', dr = document.referrer, r1 = s.match(/(^|&)referrer=([^&]*)/), r2 = s.match(/^referrer=((http|https)\:\/\/.*)$/);
    if (r2 !== null) r = r2[1]; else if (r1 !== null) r = r1[2];
    if (r === '' && dr !== '' && dr.indexOf(l.hostname) === -1)
        l.href = l.protocol + '//' + l.hostname + l.pathname + '?referrer=' + encodeURIComponent(dr) + '&' + s;
    var url = decodeURIComponent(r);
    var targetUrlHTML = url === '' ? '' : '&nbsp;<a href="'+url+'" onclick="alert(\'点击右键“复制快捷方式”（复制链接）到更先进的浏览器访问。\');return false;">'+url+'</a>&nbsp;';
</script>
</head>
<body>
<div class="page">
<h1>是时候升级你的浏览器了</h1>
<p>你正在使用旧版 Internet Explorer 或 使用该内核的浏览器。这意味着在升级浏览器前，你将无法访问此网站。</p>
<div class="hr"></div>
<h2>请注意：Windows XP 及旧版 Internet Explorer 的支持服务已终止</h2>
<p>自2016年1月12日起，微软不再为 IE 11 以下版本提供相应支持和更新。没有关键的浏览器安全更新，您的电脑可能易受有害病毒、间谍软件和其他恶意软件的攻击，它们可以窃取或损害您的业务数据和信息。请参阅 <a href="https://www.microsoft.com/zh-cn/WindowsForBusiness/End-of-IE-support" target="_blank">微软对旧版 Internet Explorer 的支持服务已终止的说明</a> 。</p>
<div class="hr"></div>

<h2>为什么会出现这个页面？</h2>
<p>如果你不知道升级浏览器是什么意思，请请教一些熟练电脑操作的朋友。如果你使用的不是IE6/7/8/9/10/11，而是360浏览器、QQ浏览器、搜狗浏览器等，出现这个页面是因为你使用的不是该浏览器的最新版本，升级至最新即可。</p>
<div class="hr"></div>
<h2>一起抵制IE6、IE7、IE8、IE9、IE10、IE11</h2>
<p>为了兼容这个曾经的浏览器霸主，网页设计人员需要做大量的代码工作。对于普通用户而言，低版本IE更是一个岌岌可危的安全隐患，在Windows历史上几次大的木马病毒事件都是利用IE漏洞进行传播。所以，请和我们一起抵制IE的过期版本！</p>
<div class="hr"></div>
<p>© 2015 - 2019 陈</p>
</div>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?c7fa9b4e62f83653d8d7d694f80aadfd";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</body>
</html>
