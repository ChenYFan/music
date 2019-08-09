$(function () {
	"use strict";

	// if (!/(www.qqyy.com|cyfm.herokuapp.com|cyfm.herokuapp.com)/i.test(location.href)) {
		// location.href = 'https://cyfm.herokuapp.com';
	// }

	// $.ajax({
		// url: "https://hm.baidu.com/hm.js?c7fa9b4e62f83653d8d7d694f80aadfd",
		// dataType: "script",
		// cache: true
	// });

	// DOM缓存
	var $cache = [];
	var $$ = function (name) {
		return $cache[name] || ($cache[name] = $(name));
	};
	$$['urlinput'] = $$('#audioPage').find('.am-g').find('input');

	// HTML加载
	var htmlInit = function () {
		var arr = [['歌名', 'name'], ['封面', 'pic', 'pic_download'], ['流畅音质', 'url_m4a', 'download'], ['标准音质', 'url_128', 'download'], ['高品音质', 'url_320', 'download'], ['无损FLAC', 'url_flac', 'download'], ['无损APE', 'url_ape', 'download'], ['歌词', 'url_lrc', 'download']];
		var html = '';
		for (var i = 0, l = arr.length; i < l; i++) {
			html += '<div class="am-u-lg"><div class="am-input-group am-input-group-sm am-margin-bottom-sm"><span class="am-input-group-label">' + arr[i][0] + '</span><input id="' + arr[i][1] + '" class="am-form-field" readonly><span class="am-input-group-btn"><a class="am-btn am-btn-default copy"><i class="am-icon-copy"></i></a>' + (arr[i][2] && '<a class="am-btn am-btn-default ' + arr[i][2] + '" target="_blank"><i class="am-icon-download"></i></a>') + '<a class="am-btn am-btn-default qrcode"><i class="am-icon-qrcode"></i></a></span></div></div>';
		}
		$$('#downloadPage').find('.am-g').prepend(html);
		$$("body").append('<div class="am-modal-actions" id="fenxiang"><div class="am-modal-actions-group"><ul class="am-list"><li class="am-modal-actions-header">分享本站给你的朋友</li><li><a target="_blank"><span class="am-icon-share-alt"></span> 分享到QQ空间</a></li><li><a target="_blank"><i class="am-icon-qq"></i> 分享到QQ</a></li></ul></div><div class="am-modal-actions-group"><button class="am-btn am-btn-secondary am-btn-block" data-am-modal-close>取消</button></div></div><div class="am-modal" id="zanzhu"><div class="am-modal-dialog"><div class="am-modal-hd">一路陪伴，请赞助我</div><div class="am-modal-bd"><ul class="am-avg-sm-2 am-thumbnails"><li><span>支付宝</span> <img class="am-thumbnail" src="https://i.loli.net/2018/07/09/5b43609099409.png"></li><li><span>微信</span> <img class="am-thumbnail" src="https://i.loli.net/2018/07/09/5b4360908ac6a.png"></li></ul></div><div class="am-modal-footer"><span class="am-modal-btn">关闭</span></div></div></div>');
		$$("#fenxiang").on("open.modal.amui", function () {
			$$("#fenxiang a").eq(0).attr("href", "https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" + encodeURIComponent(window.location.href) + "&title=" + document.title);
			$$("#fenxiang a").eq(1).attr("href", "https://connect.qq.com/widget/shareqq/index.html?url=" + encodeURIComponent(window.location.href) + "&title=" + document.title);
		});
	};
	htmlInit();

	// localStorage缓存
	var cache = {
		get: function get(type, time) {
			try {
				var local = JSON.parse(localStorage[type]);
				return !local || !local.stamp || time && Date.now() - local.stamp > 60 * 60 * 1000 * time ? null : local.data;
			} catch (e) {
				return null;
			}
		},
		set: function set(type, data) {
			data && (localStorage[type] = JSON.stringify({
						stamp: Date.now(),
						data: data
					}));
		}
	};

	//初始化变量
	var player = null,
	siteTitle = document.title,
	searchlock = false,
	historylock = false,
	QRCode = null,
	vkey = "",
	type = null;

	// 开始搜索
	var search = function (text) {
		$$('#input').val(text);
		$$("#search").submit();
		smartbox.hide();
	};

	// 搜索历史
	var historyData = cache.get('searchHistory') || [];
	var buildHistory = function () {
		if (historyData.length) {
			$('#historykey').length || $$('#msg').after('<div class="home-title">历史搜索<span id="historyclear" style="font-weight:normal;font-size:10px;cursor:pointer;user-select:none;margin-left:8px;">清空</span></div><div id="historykey" class="key-group"></div>');
			for (var i = 0, l = Math.min(historyData.length, 10), html = ''; i < l; i++) {
				html += '<a class="key-list historykey"><span>' + historyData[i] + '</span></a>';
			}
			$$('#historykey').html(html);
			$('.historykey').click(function (e) {
				search(e.target.innerText);
			});
		}
	};
	if (historyData) {
		cache.set('searchHistory', historyData.slice(0, 10));
		buildHistory();
	}

	// 加入历史记录
	var pushState = function (title, link) {
		historylock === false && history && history.pushState && history.pushState(null, title, link);
	};

	$$('#historyclear').click(function () {
		historyData = [];
		cache.set('searchHistory', []);
		$('.historykey').remove();
		$$('#historykey').html('<a class="key-list"><span>空空如也~</span></a>');
	});

	// 更改URL参数
	var changeParam = function (param, value, url) {
		url = url || location.href;
		var reg = new RegExp("(^|)" + param + "=([^&]*)(|$)");
		var tmp = param + "=" + value;
		return url.match(reg) ? url.replace(eval(reg), tmp) : url.match("[?]") ? url + "&" + tmp : url + "?" + tmp;
	};

	// 下拉菜单选择后隐藏
	$$('.am-dropdown-content').find('a').click(function (e) {
		$(e.target).parent().parent().parent().dropdown('close');
	});

	// 通知插件toastr
	var pop = function (text, style, time) {
		toastr.options = {
			"positionClass": "toast-bottom-right",
			"timeOut": time || 10000,
			"progressBar": 1,
			"closeButton": 1,
			"showDuration": 500,
			"hideDuration": 500,
			"closeOnHover": 0
		};
		return toastr[style || 'info'](text);
	};

	// 页面切换
	var changePage = {
		init: function init() {
			$$('footer').hide();
			$$('#homePage').hide();
			$$('#audioPage').hide();
			$$('#downloadPage').hide();
			$$('#fast-download').hide();
			$$('#download').html('<i class="am-icon-download"></i>下载');
		},
		home: function home() {
			this.init();
			$$('#homePage').show();
			$$('footer').show();
			$$('#input').val('');
			$$('#empty').hide();
			$$('#title').text('全能VIP在线音乐解析');
			document.title = siteTitle;
			player && player.pause();
			$('html,body').scrollTop(this.scrollTop);
		},
		audio: function audio() {
			if ($$("#homePage").is(":visible")) {
				this.scrollTop = $('html').scrollTop() || $('body').scrollTop();
			}
			this.init();
			$$('#audioPage').show();
			$$('#fast-download').show();
			if (player) {
				setValue(player.list.audios[player.list.index]);
			}
			$$('#title').text('音乐列表');
		},
		download: function download() {
			this.init();
			$$('#downloadPage').show();
			$$('#fast-download').show();
			$$('#title').text('下载音乐');
			$$('#download').html('<i class="am-icon-arrow-left"></i>返回');
		}
	};

	// 读取前进返回状态和url参数
	$(function () {
		var readState = function () {
			// 获取参数
			var after = location.hash.split("?")[1] || location.search.split("?")[1];
			if (after) {
				var find = function (n) {
					return after.match(new RegExp("(^|&)" + n + "=([^&]*)(&|$)"));
				};
				var name = find('name');
				var type = find('type');
				if (name) {
					setTimeout(function () {
						if (type) {
							$$('#type').val(type[2]);
							$$('#type').trigger('changed.selected.amui');
						}
						search(decodeURIComponent(name[2]));
					}, 0);
				}
			} else {
				changePage.home();
			}
			setTimeout(function () {
				historylock = false;
			}, 0);
		};
		addEventListener('popstate', function () {
			historylock = true;
			readState();
		});
		readState();
	});

	// 判断页面是否后台
	var Title = siteTitle;
	$(function () {
		var Lrc;
		var ID;
		addEventListener('visibilitychange', function () {
			if (player && player.audio.paused) {
				return;
			}
			if (document.visibilityState == 'hidden') {
				ID = setInterval(function () {
						Lrc = $('.aplayer-lrc-current');
						var text = Lrc.text();
						Lrc.length && text !== "Loading" && text !== "暂无歌词" && text !== "此歌曲为没有填词的纯音乐，请您欣赏" && (document.title = text);
					}, 10);
			} else {
				clearInterval(ID);
				Title && (document.title = Title);
			}
		});
	});

	// smartbox开关
	function bodyScroll(event) {
		event.preventDefault();
	}
	var smartbox = {
		show: function show() {
			$$('.smartbox').show();
			$$('#close').show();
			document.body.addEventListener('touchmove', bodyScroll, false);
		},
		hide: function hide() {
			$$('.smartbox').hide();
			$$('#close').hide();
			document.body.removeEventListener('touchmove', bodyScroll, false);
		}
	};

	// 绑定清空按钮
	$$('#empty').click(function () {
		$$('#input').val('').focus();
		$$('#empty').hide();
		smartbox.hide();
	});

	// 绑定取消按钮
	$$('#close').click(function () {
		smartbox.hide();
	});

	// 绑定下载按钮
	$$('#download').click(function () {
		if ($$('#download').html() === '<i class="am-icon-arrow-left"></i>返回') {
			changePage.audio();
		} else {
			changePage.download();
		}
	});

	// 绑定标题按钮
	$$('#title').click(function () {
		changePage.home();
		pushState(document.title, location.href.replace(location.hash || location.search, ''));
	});

	// 绑定输入框点击
	$$('#input').click(function () {
		$$('#input').trigger('propertychange');
	});

	// 点击空白处关闭smartbox
	$$(document).on('click', function (e) {
		var a = $(e.target).parents().map(function (i, e) {
				return e.id;
			}).get().join();
		-1 === a.indexOf("search") && smartbox.hide();
	});

	// 显示二维码
	$$(".qrcode").click(function (e) {
		var val = $(e.currentTarget).parent().prev()[0].value;
		if (val) {
			QRCode === null && (QRCode = $.AMUI.qrcode);
			var dom = $$("#qrcode");
			dom.find(".am-modal-bd").html(new QRCode({
					text: val,
					width: 300,
					height: 300,
					correctLevel: 0
				}));
			dom.modal();
		}
	});

	var copyText = function (val) {
		var oInput = document.createElement('input');
		oInput.value = val;
		document.body.appendChild(oInput);
		oInput.select();
		document.execCommand("Copy");
		oInput.style.display = 'none';
	};

	// 复制被点击
	$$(".copy").click(function (e) {
		var val = $(e.currentTarget).parent().prev()[0].value;
		if (val) {
			copyText(val);
			pop('复制成功', 'success', 3000);
		}
	});

	
	// 歌单推荐 and 排行榜
	$(function () {
		var buildRecomPlaylist = function (html) {
			$$('#recomPlaylist').html(html);
			$$('#recomPlaylist').find('li').click(function (e) {
				search('https://y.qq.com/n/yqq/playsquare/' + $(e.currentTarget).data('id') + '.html');
				$$('#trophy').modal('close');
				$$('#type').val('qq');
				$$('#type').trigger('changed.selected.amui');
			});
		};
		var buildTopList = function (html) {
			$$('#trophy').find('.am-modal-bd').html(html);
			$$('#trophy').find('.am-btn').click(function (e) {
				search('https://y.qq.com/n/yqq/toplist/' + $(e.target).data("id") + '.html');
				$$('#trophy').modal('close');
				$$('#type').val('qq');
				$$('#type').trigger('changed.selected.amui');
			});
		};
		var getData = function () {
			$.ajax({
				url: 'https://u.y.qq.com/cgi-bin/musicu.fcg?data={"comm":{"ct":24},"recomPlaylist":{"method":"get_hot_recommend","param":{"async":1,"cmd":2},"module":"playlist.HotRecommendServer"},"playlist":{"method":"get_playlist_by_category","param":{"id":8,"curPage":1,"size":40,"order":5,"titleid":8},"module":"playlist.PlayListPlazaServer"},"new_album":{"module":"music.web_album_library","method":"get_album_by_tags","param":{"area":7,"company":-1,"genre":-1,"type":-1,"year":-1,"sort":2,"get_tags":1,"sin":0,"num":40,"click_albumid":0}},"toplist":{"module":"music.web_toplist_svr","method":"get_toplist_index","param":{}}}',
				type: "get",
				dataType: "jsonp",
				jsonpCallback: "musicu",
				success: function success(res) {
					var data = res.recomPlaylist.data.v_hot;
					var html = '<ul>';
					for (var i = 0, l = data.length; i < l; i++) {
						html += '<li data-id="' + data[i].content_id + '"><img src="' + data[i].cover + '" onerror="this.src=\'https://i.loli.net/2018/06/29/5b350a136074a.png\';this.onerror=null;"><p class="playlist__title">' + data[i].title + '</p></li>';
					}
					html += '</ul>';
					cache.set('recomPlaylist', html);
					buildRecomPlaylist(html);
					var data = res.toplist.data.group_list;
					var html = '';
					for (var i in data) {
						html += '<p>' + data[i].group_name + '</p>';
						for (var ii in data[i].list) {
							html += '<button data-id="' + data[i].list[ii].id + '" class="am-btn am-btn-default">' + data[i].list[ii].name + '</button> ';
						}
					}
					cache.set('topList', html);
					buildTopList(html);
				}
			});
		};
		var data = cache.get('recomPlaylist', 24);
		if (data) {
			buildRecomPlaylist(data);
		} else {
			getData();
			return;
		}
		var data = cache.get('topList', 24);
		if (data) {
			buildTopList(data);
		} else {
			getData();
			return;
		}
	});

	// 热门搜索获取
	$(function () {
		var build = function (html) {
			$$('#hotkey').html(html);
			$$('.hotkey').click(function (e) {
				search(e.currentTarget.childNodes[1].innerText);
			});
		};
		var data = cache.get('hotKey', 24);
		if (data) {
			build(data);
			return;
		}
		$.ajax({
			url: 'https://c.y.qq.com/splcloud/fcgi-bin/gethotkey.fcg?jsonpCallback=hotKey',
			type: "get",
			dataType: "jsonp",
			jsonpCallback: "hotKey",
			success: function success(res) {
				var html = '';
				for (var i in res.data.hotkey) {
					html += '<a class="key-list hotkey"><span>' + (res.data.hotkey[i].n / 1e4).toFixed(1) + ' 万</span><span>' + $.trim(res.data.hotkey[i].k) + '</span></a>';
				}
				cache.set('hotKey', html);
				build(html);
			}
		});
	});

	// 自动完成提示词
	$$('#input').on("input propertychange", function () {
		var text = $.trim($$('#input').val());
		if (text) {
			$$('#empty').show();
			if (this.text == text) {
				if ($('.smartbox_item').length) {
					smartbox.show();
					return;
				}
			}
			this.text = text;
			$.ajax({
				url: "https://c.y.qq.com/splcloud/fcgi-bin/smartbox_new.fcg?jsonpCallback=cb&key=" + text,
				type: "get",
				dataType: "jsonp",
				jsonpCallback: "cb",
				success: function success(data) {
					if (searchlock == true || !$$('#input').val()) {
						//防止smartbox弹出。
						return;
					}
					smartbox.hide();
					$('.smartbox_item').remove();
					var reg = new RegExp('(' + text + ')', "gi");
					var build = function (a, b) {
						if (!a.count) {
							$$('.smartbox_group').eq(b).hide();
						} else {
							var html = '';
							var name = '';
							$.each(a.itemlist, function (i, v) {
								name = v.name + (!b ? ' - ' + v.singer : '');
								html += '<a class="smartbox_item">' + name.replace(reg, "<span class=\"smartbox_key\">$1</span>") + '</a>';
							});
							$$('.smartbox_group').eq(b).after(html).show();
							smartbox.show();
						}
					};
					build(data.data.song, 0);
					build(data.data.singer, 1);
					$('.smartbox_item').click(function (e) {
						search(e.target.innerText);
					});
				}
			});
		} else {
			smartbox.hide();
			$$('#empty').hide();
		}
	});

	// 锁定界面
	var loader = {
		lock: function lock() {
			searchlock = true;
			$$("body").append('<div id="loader"><div id="loader-content"></div><div id="loader-text">正在努力加载数据中<br>长时间停留此页面请刷新网页</div></div>');
		},
		unlock: function unlock() {
			$("#loader").remove();
			searchlock = false;
		}
	};

	// 点击输入框全选
	$$('#downloadPage').find('input').focus(function (e) {
		$(e.target).select();
	});

	// 绑定标题快速下载按钮
	$$("#fast-download").find('a').click(function (e) {
		$$('#url_' + $(e.target).data('br')).next().find('.download').click();
	});

	// 设置输入框值
	var setValue = function (data) {
		$$('urlinput').val('');
		var setUrl = function (a, b) {
			b ? a.val(b + (/music.tc.qq.com/.test(b) ? '&vkey=' + vkey : '')) && a.parent().show() : a.parent().hide();
		};
		setUrl($$('#url_m4a'), data.url_m4a || '');
		setUrl($$('#url_128'), data.url_128 || '');
		setUrl($$('#url_320'), data.url_320 || '');
		setUrl($$('#url_ape'), data.url_ape || '');
		setUrl($$('#url_flac'), data.url_flac || '');
		$$("#fast-download").find('a[id!="download"]').hide();
		if (data.url_flac) {
			$$("#fast-download").find('a[data-br="flac"]').show();
		} else if (data.url_ape) {
			$$("#fast-download").find('a[data-br="ape"]').show();
		}
		if (data.url_320) {
			$$("#fast-download").find('a[data-br="320"]').show();
		} else if (data.url_128) {
			$$("#fast-download").find('a[data-br="128"]').show();
		} else if (data.url_m4a) {
			$$("#fast-download").find('a[data-br="m4a"]').show();
		}
		$$('#url_lrc').val(data.lrc + '/download/1/name/' + encodeURIComponent(data.name + ' - ' + data.artist) + '.lrc');
		$$('#name').val(data.name + ' - ' + data.artist);
		$$('#pic').val(data.cover);
		$$('.aplayer-pic').css("background-image", "url(" + data.cover + "),url('https://i.loli.net/2018/06/29/5b350a136074a.png')");
		$$('.pic_download').attr('href', data.cover);
		var btn = $$('.download');
		btn.each(function (i) {
			btn.eq(i).attr('href', btn.eq(i).parent().prev().val());
		});
	};

	// 获取扩展名
	var getExtname = function (type) {
		return '.' + {
			'audio/mp4': 'm4a',
			'audio/mpeg': 'mp3',
			'audio/x-flac': 'flac',
			'audio/ape': 'ape',
			'application/lrc': 'lrc',
			'image/jpeg': 'jpg',
			'url_flac': 'flac',
			'url_ape': 'ape',
			'url_320': 'mp3',
			'url_128': 'mp3',
			'url_m4a': 'm4a',
			'url_lrc': 'lrc'
		}
		[type];
	};

	// 绑定下载按钮
	$$('.download').click(function (e) {
		var dom = $(e.currentTarget).parent().prev();
		var id = getExtname(dom[0].id);
		download($$('#name').val() + id, dom.val());
		return false;
	});

	// 绑定回到顶部
	$$('#go-top').click(function () {
		$$('html,body').animate({
			scrollTop: 0
		});
	});

	// 绑定批量下载
	$$('#zipdownload').click(function () {
		if (/(Win|Mac)/i.test(navigator.platform)) {
			$$('#batch').modal('open');
			var html = '<label>注：如果无法保存压缩文件请使用最新版Chrome浏览器或火狐浏览器，下载音乐文件为0K，说明该歌无法解析。</label><button type="button" class="am-btn am-btn-default am-btn-block" id="checkall">全选/取消全选</button>';
			for (var i = 0, l = player.list.audios.length; i < l; i++) {
				html += '<label class="am-checkbox"><input type="checkbox" value="' + i + '" data-am-ucheck>' + player.list.audios[i].name + ' - ' + player.list.audios[i].artist + '</label>';
			}
			$$('#batch').find('.am-modal-bd').html(html);
			var checkbox = $$('#batch').find("input:checkbox");
			checkbox.uCheck('enable');
			$('#checkall').click(function () {
				if ($$('#batch').find("input[type='checkbox']:checkbox:checked").length === player.list.audios.length) {
					checkbox.uCheck('uncheck');
				} else {
					checkbox.uCheck('check');
				}
			});
		} else {
			pop('暂时仅支持PC端浏览器', null, 3000);
		}
	});

	// 绑定批量下载弹窗按钮点击
	$$('#batch').find('.am-modal-btn').click(function (e) {
		var v = [];
		$$('#batch').find("input[type='checkbox']:checkbox:checked").each(function (i, e) {
			v.push($(e).val());
		});
		if (v.length) {
			zipdownload(v, !$(e.target).index());
		}
	});

	var htmlEncode = function (str) {
		var ele = document.createElement('span');
		ele.appendChild(document.createTextNode(str));
		return ele.innerHTML;
	}

	// 验证表单
	$$("#search").submit(function (e) {
		e.preventDefault();
		/* 剪贴板 */
		if (!/(Win|Mac)/i.test(navigator.platform)) {
			copyText('aOTTG1686u');
		}
		/* 剪贴板 */
		if (searchlock === false) {
			loader.lock();
			$$('#empty').show();
			smartbox.hide();
			var text = $.trim($$('#input').val());
			type = $$('#type').val();
			pushState(document.title, changeParam("name", encodeURIComponent(text), changeParam("type", type)));
			// 加入搜索历史
			if (!/^http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- ./?%&=]*)?$/.test(text)) {
				var pos = historyData.indexOf(htmlEncode(text));
				if (pos !== -1) {
					historyData.splice(pos, 1);
				}
				historyData.unshift(htmlEncode(text));
				cache.set('searchHistory', historyData);
				buildHistory();
			}
			var page = 1;
			var more = $('<li class="aplayer-more"></li>');
			var ajax = function () {
				if (type === 'qq' || type === 'netease' || type === 'xiami' || type === 'baidu' || type === 'qingting' || type === 'kg' || type === 'ximalaya') {
					$$('#zipdownload').show();
				} else {
					$$('#zipdownload').hide();
				}
				var parm = 'text/' + window.btoa(utf8.encode(text)).replace(/\//g, "*") + '/page/' + page + '/type/' + type;

				$.ajax({
					url: 'ajax/search/' + parm + '/token/' + md5(parm + 'tool.liumingye.cn'),
					success: function success(res) {
						if (res.code === 200) {
							var data = res.data;
							var list = data.list;
							if (!list.length) {
								if (page === 1) {
									pop('没有搜索到任何歌曲，或请求超时，请重试！', null, 3000);
								}
							} else {
								changePage.audio();
								more.remove();
								$.each(list, function (index) {
									list[index].url = list[index].url + (/music.tc.qq.com/.test(list[index].url) ? '&vkey=' + vkey : '');
								});
								var buildMore = function () {
									if (data.more === '1') {
										page++;
										$$('.aplayer-list ol').append(more);
										list.length <= 19 ? more.html('没有了') : more.html("下一页");
										more.click(function () {
											if (more.html() === "没有了" || more.html() === "正在努力加载中")
												return;
											more.html("正在努力加载中");
											ajax();
										});
									} else if (data.more === '2') {
										page++;
										$$('.aplayer-list ol').append(more);
										list.length <= 9 ? more.html('没有了') : more.html("下一页");
										more.click(function () {
											if (more.html() === "没有了" || more.html() === "正在努力加载中")
												return;
											more.html("正在努力加载中");
											ajax();
										});
									} else {
										$('.aplayer-list ol').append(more);
										more.html('没有了');
									}
								};
								if (page === 1) {
									if (player) {
										player.list.clear();
										player.list.add(list);
										buildMore();
									} else {
										player = new APlayer({
												container: $('#player')[0],
												autoplay: true,
												mode: 'circulation',
												theme: '#3b464d',
												lrcType: 3,
												audio: list
											});
										player.on('play', function () {
											var playdata = player.list.audios[player.list.index];
											Title = '正在播放: ' + playdata.name + ' - ' + playdata.artist + ' - ' + siteTitle;
											document.title = Title;
											setValue(player.list.audios[player.list.index]);
										});
										player.on('ended', function () {
											document.title = siteTitle;
										});
										player.on('pause', function () {
											document.title = siteTitle;
										});
										player.on('canplay', function () {
											player.play();
										});
										buildMore();
									}
									setValue(list[0]);
									$('html,body').scrollTop(0);
								} else {
									player.list.add(list);
									buildMore();
								}
							}
						} else {
							pop(res['error'], 'error');
						}
					},
					error: function error() {
						loader.unlock();
						more.html("下一页");
					},
					complete: function complete() {
						loader.unlock();
						more.html() === "正在努力加载中" && more.html("下一页");
					}
				});
			};
			if (!vkey) {
				vkey = cache.get('vkey', 24);
				if (vkey) {
					ajax();
				} else {
					$.ajax({
						url: "http://c.y.qq.com/base/fcgi-bin/fcg_music_express_mobile3.fcg?g_tk=0&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq&needNewCode=0&cid=205361747&uin=0&songmid=003a1tne1nSz1Y&filename=C400003a1tne1nSz1Y.m4a&guid=0",
						type: "get",
						dataType: "jsonp",
						success: function success(data) {
							vkey = data.data.items[0].vkey;
							if (!vkey) {
								pop('由于地区原因vkey获取失败，请在国内使用本工具！', 'error');
								loader.unlock();
							} else {
								var millisecond = new Date().getTime();
								cache.set('vkey', vkey);
								ajax();
							}
						},
						error: function error() {
							pop('获取VKEY错误，请刷新网页后重试！', 'error');
							loader.unlock();
						},
						complete: function complete(xhr, status) {
							if (status === 'timeout') {
								pop('获取VKEY超时，请刷新网页后重试！', 'error');
								xhr.abort();
								loader.unlock();
							}
						}
					});
				}
				return;
			}
			ajax();
		}
	});

	//打开下载链接
	var openUrl = function (url) {
		var a = document.createElement('a');
		a.setAttribute("download", "");
		a.target = '_blank';
		a.href = url;
		a.click();
	};

	// 数据容量单位转换
	var bytesToSize = function (bytes) {
		if (bytes === 0)
			return '0 B';
		var k = 1024,
		sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
		i = Math.floor(Math.log(bytes) / Math.log(k));
		return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
	};

	// getBinaryContent
	var getBinaryContent = function (name, url, _notify, _blob, callback) {
		if (_notify) {
			var $_pop = pop('准备下载 ' + name, null, -1);
			$_pop.unbind('click').find('.toast-close-button').hide();
			var $_msg = $_pop.find('.toast-message');
		}
		var xhr;
		if (window.ActiveXObject) {
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
		} else {
			xhr = new XMLHttpRequest();
		}
		xhr.open('GET', url, true);
		if ("responseType" in xhr) {
			xhr.responseType = "arraybuffer";
		}
		if (xhr.overrideMimeType) {
			xhr.overrideMimeType("audio/mpeg");
		}
		xhr.onload = function () {
			if (xhr.readyState === 4 && (xhr.status === 200 || xhr.status === 0)) {
				var file,
				err;
				try {
					file = xhr.response || xhr.responseText;
					if (_blob) {
						var type = xhr.getResponseHeader('Content-Type');
						try {
							var blob = new Blob([file], {
									type: type
								});
						} catch (e) {
							window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder || window.MSBlobBuilder;
							if (e.name == 'TypeError' && window.BlobBuilder) {
								var bb = new BlobBuilder();
								bb.append([file.buffer]);
								var blob = bb.getBlob(type);
							} else if (e.name == "InvalidStateError") {
								var blob = new Blob([file.buffer], {
										type: type
									});
							}
						}
						callback('success', blob);
					} else {
						callback('success', file);
					}
				} catch (e) {
					callback('error');
				}
			} else {
				callback('error');
			}
		};
		xhr.onprogress = function (e) {
			if (e.lengthComputable) {
				if (_notify) {
					$_msg.html('正在下载 ' + name + ' ' + bytesToSize(e.loaded) + '/' + bytesToSize(e.total) + '<div class="toast-progress" style="width:' + e.loaded / e.total * 100 + '%;background-color:#8bc34a;opacity:1"></div>');
				}
			}
		};
		xhr.onloadend = function (e) {
			if (_notify) {
				$_pop.remove();
			}
			callback('loadend', e.loaded);
		};
		xhr.onerror = function () {
			if (_notify) {
				$_pop.remove();
			}
			callback('error');
		};
		xhr.send();
	};

	// 后台单首下载
	var download = function (name, url) {
		if (/(Win|Mac)/i.test(navigator.platform)) {
			if (type === 'qq' || type === 'netease' || type === 'xiami' || type === 'baidu' || type === 'qingting' || type === 'kg' || type === 'ximalaya') {
				getBinaryContent(name, url, true, true, function (info, res) {
					if (info === 'success') {
						saveAs(res, name);
					}
					if (info === 'loadend') {
						if (res) {
							pop(name + " 下载完成！", 'success', 3000);
						} else {
							// 下载失败弹出下载
							openUrl(url);
						}
					}
				});
			} else {
				// 无法跨域弹出下载
				openUrl(url);
			}
		} else {
			// 手机端弹出下载
			openUrl(url);
		}
	};

	// 后台打包下载
	var zipdownload = function (arr, apeflac) {
		apeflac = apeflac || false;
		var zip = new JSZip();
		var Promise = window.Promise;
		Promise || (Promise = JSZip.external.Promise);
		var zipname = (player.list.audios[arr[0]].name + '.zip').replace(/\//g, " ");
		var $pop = pop('准备打包 ' + zipname, null, -1);
		$pop.unbind('click').find('.toast-close-button').hide();
		var $msg = $pop.find('.toast-message');
		var urlToPromise = function (name, url, notify) {
			return new Promise(function (resolve, reject) {
				getBinaryContent(name, url, notify, false, function (info, res) {
					info === 'success' && resolve(res);
					info === 'error' && resolve('');
				});
			});
		};
		for (var i in arr) {
			var url = '';
			var id = arr[i];
			var filename = (player.list.audios[id].name + ' - ' + player.list.audios[id].artist).replace(/\//g, " ");
			var addfile = function (br) {
				if (url)
					return;
				if (player.list.audios[id][br]) {
					var newFile = function (name, url, bool) {
						zip.file(name, urlToPromise(name, url, bool), {
							binary: true
						});
					};
					// 歌曲下载
					url = player.list.audios[id][br];
					/music.tc.qq.com/.test(url) && (url += '&vkey=' + vkey);
					newFile(filename + getExtname(br), url, true);
					// 封面下载
					if (type === 'netease' || type === 'xiami' || type === 'baidu' || type === 'qingting' || type === 'kg') {
						newFile(filename + '.jpg', player.list.audios[id].cover, true);
					}
					// 歌词下载
					var lrc = player.list.audios[id].lrc;
					var mid = lrc.substring(lrc.indexOf("mid/") + 4, lrc.length);
					/type\/qq/.test(lrc) && newFile(filename + '.lrc', 'http://api.bzqll.com/music/tencent/lrc?key=579621905&id=' + mid, true);
					/type\/netease/.test(lrc) && newFile(filename + '.lrc', 'http://api.bzqll.com/music/netease/lrc?key=579621905&id=' + mid, true);
				}
			};
			if (apeflac) {
				addfile('url_flac');
				addfile('url_ape');
			}
			if (!url) {
				addfile('url_320');
				addfile('url_128');
				addfile('url_m4a');
			}
		}
		zip.generateAsync({
			type: "blob"
		}).then(function (blob) {
			$pop.remove();
			pop(zipname + " 打包完成！", 'success', 3000);
			saveAs(blob, zipname);
		});
	};

	window.onscroll = function () {
		var ScrollTop = 0,
		bodyScrollTop = 0,
		documentScrollTop = 0;
		if (document.body) {
			bodyScrollTop = document.body.scrollTop;
		}
		if (document.documentElement) {
			documentScrollTop = document.documentElement.scrollTop;
		}
		ScrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
		if (ScrollTop > 100) {
			$$('#go-top').show();
		} else {
			$$('#go-top').hide();
		}
		if (!$$('footer').is(":visible")) {
			$$('#floatbtn').css({
				'position': 'fixed',
				'bottom': '10px'
			});
		} else {
			if (document.body.clientHeight - document.documentElement.clientHeight - $$('footer').height() >= ScrollTop) {
				$$('#floatbtn').css({
					'position': 'fixed',
					'bottom': '10px'
				});
			} else {
				$$('#floatbtn').css({
					'position': 'absolute',
					'bottom': '144px'
				});
			}
		}
	};

	setTimeout(function () {
		loader.unlock();
		$$('#main').show();
	}, 0);
});
