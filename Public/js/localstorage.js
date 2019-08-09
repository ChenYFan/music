var whir = window.whir || {};
whir.res = {
	getContent: function (url, complete, error) {
		var xhr = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
		if (xhr) {
			xhr.open("GET", url, false);
			xhr.onreadystatechange = function () {
				if (xhr.readyState == 4 && xhr.status == 200) {
					complete(xhr.response || xhr.responseText);
				}
			};
			xhr.onerror = function () {
				error();
			};
			xhr.ontimeout = function () {
				error();
			};
			xhr.send();
		} else {
			error();
		}
	},
	loadJs: function (name, url, version, callback) {
		var This = this;
		name = 'js.' + name;
		if (window.localStorage) {
			var js = localStorage.getItem(name);
			if (!js || js.length == 0 || version != localStorage.getItem(name + ".v")) {
				console.log('download ' + name);
				This.getContent(url, function (res) {
					localStorage.setItem(name, JSON.stringify(res));
					localStorage.setItem(name + ".v", version);
					res = res == null ? "" : res;
					This.writeJs(res);
					callback && callback();
				}, function () {
					This.linkJs(url);
				});
			} else {
				console.log('cache ' + name);
				This.writeJs(JSON.parse(js));
				callback && callback();
			}
		} else {
			console.log('link ' + name);
			This.linkJs(url, callback);
		}
	},
	loadCss: function (name, url, version) {
		var This = this;
		name = 'css.' + name;
		if (window.localStorage) {
			var css = localStorage.getItem(name);
			if (!css || css.length == 0 || version != localStorage.getItem(name + ".v")) {
				console.log('download ' + name);
				This.getContent(url, function (res) {
					localStorage.setItem(name, JSON.stringify(res));
					localStorage.setItem(name + ".v", version);
					res = res == null ? "" : res;
					This.writeCss(res);
				}, function () {
					This.linkCss(url);
				});
			} else {
				console.log('cache ' + name);
				This.writeCss(JSON.parse(css));
			}
		} else {
			console.log('link ' + name);
			This.linkCss(url);
		}
	},
	writeJs: function (text) {
		var link = document.createElement("script");
		link.innerHTML = text;
		document.getElementsByTagName('head').item(0).appendChild(link);
	},
	linkJs: function (url, callback) {
		var link = document.createElement("script");
		link.src = url;
		document.getElementsByTagName('head').item(0).appendChild(link);
		link.onload = link.onreadystatechange = function () {
			if (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") {
				callback && callback();
			}
		};
	},
	writeCss: function (text) {
		var link = document.createElement("style");
		link.type = "text/css";
		link.innerHTML = text;
		document.getElementsByTagName('head').item(0).appendChild(link);
	},
	linkCss: function (url) {
		var link = document.createElement("link");
		link.type = "text/css";
		link.rel = "stylesheet";
		link.href = url;
		document.getElementsByTagName('head').item(0).appendChild(link);
	}
};
var cdn = 'https://cdnjs.loli.net/ajax/libs/';
whir.res.loadCss("aplayer", cdn + "aplayer/1.10.1/APlayer.min.css", "1.10.1");
whir.res.loadCss("toastr", cdn + "toastr.js/2.1.4/toastr.min.css", "2.1.4");
whir.res.loadJs("jquery", cdn + "jquery/3.3.1/jquery.min.js", "3.3.1", function () {
	whir.res.loadJs("amazeui", cdn + "amazeui/2.7.2/js/amazeui.min.js", "2.7.2");
	whir.res.loadJs("aplayer", cdn + "aplayer/1.10.1/APlayer.min.js", "1.10.1");
	whir.res.loadJs("toastr", cdn + "toastr.js/2.1.4/toastr.min.js", "2.1.4");
	whir.res.loadJs("jszip", cdn + "jszip/3.1.5/jszip.min.js", "3.1.5");
	whir.res.loadJs("filesaver", cdn + "FileSaver.js/1.3.8/FileSaver.min.js", "1.3.8");
	whir.res.loadJs("utf8", cdn + "utf8/3.0.0/utf8.min.js", "3.0.0");
	whir.res.loadJs("base64", cdn + "Base64/1.0.1/base64.min.js", "1.0.1");
	whir.res.loadJs("md5", cdn + "blueimp-md5/2.10.0/js/md5.min.js", "2.10.0");
	// whir.res.loadJs("vconsole", cdn + "vConsole/3.2.0/vconsole.min.js", "3.2.0",function(){window.vConsole = new window.VConsole()});
	// whir.res.loadJs("main", "Public/js/main.js?v=20190225", "20190225")
	whir.res.loadJs("main", "Public/js/main.js", Date().split(' ')[4]);
});
