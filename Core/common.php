<?php if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
	exit;
}
?>
<?php
/*
 *系统函数库
 */
/**
 * 替代count
 * @param array $array_or_countable 数组
 * @param integer $mode 模式
 * @return integer
 */
function f_count($array_or_countable, $mode = COUNT_NORMAL) {
	if (is_array($array_or_countable) || is_object($array_or_countable)) {
		return count($array_or_countable, $mode);
	} else {
		return 0;
	}
}
/**
 * 发送get和post的请求方式
 * @param string $url 网址
 * @param array $post POST数据
 * @param array $header 设置Header区域内容
 * @param array $cookie 需要传递的cookie数据
 * @param bool $returnHeader 当该参数为 TRUE 时，将返回 Header区域内容 而非 返回值
 * @return string
 */
function curl_request($url, $post = '', $header = array('Expect: '), $cookie = '', $returnHeader = 0) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36');
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_REFERER, 'https://y.qq.com/');
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
	curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	if ($post) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
	}
	if ($cookie) {
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	}
	curl_setopt($curl, CURLOPT_HEADER, $returnHeader);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	if (curl_errno($curl)) {
		return curl_error($curl);
	}
	curl_close($curl);
	if ($returnHeader) {
		list($header, $body) = explode("\r\n\r\n", $data, 2);
		preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
		$info['cookie'] = substr($matches[1][0], 1);
		$info['content'] = $body;
		return $info;
	} else {
		if (!$data) {
			response('', 504, '服务器解析超时，请重试！');
			exit();
		} else {
			return $data;
		}
	}
}
/**
 * jsonp 转 json
 * @param string $jsonp 待解码的jsonp格式的字符串。
 * @param bool $jsonp 当该参数为 TRUE 时，将返回 array 而非 object
 * @return string
 */
function jsonp_decode($jsonp, $assoc = true) {
	if ($jsonp[0] !== '[' && $jsonp[0] !== '{') {
		$jsonp = mb_substr($jsonp, mb_strpos($jsonp, '('));
	}
	$json = trim($jsonp, "();");
	if ($json) {
		return json_decode($json, $assoc);
	}
}
/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @param integer $showPage 是否显示状态页面
 * @return void
 */
function send_http_status($code, $showPage = false) {
	static $_status = array(
		// Informational 1xx
		100 => 'Continue', 101 => 'Switching Protocols',
		// Success 2xx
		200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',
		// Redirection 3xx
		300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Moved Temporarily ', // 1.1
		303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',
		// Client Error 4xx
		400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed',
		// Server Error 5xx
		500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 509 => 'Bandwidth Limit Exceeded');
	if (isset($_status[$code])) {
		header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
		// 确保FastCGI模式下正常
		header('Status:' . $code . ' ' . $_status[$code]);
	}
	if ($showPage && file_exists(Public_PATH . $code . '.php')) {
		include Public_PATH . $code . '.php';
	}
}
/**
 * 按json方式输出通信数据
 * @param array $data 数据
 * @param integer $code 状态码
 * @param string $message 提示信息
 * @return string
 */
function response($data, $code = 200, $message = '未知错误') {
	send_http_status($code);
	header('Content-type:text/json');
	echo json_encode(array('code' => $code, 'data' => $data, 'error' => $message), JSON_UNESCAPED_UNICODE);
}
/**
 * HTML代码压缩
 * @param string $html_source 需要压缩的HTML源代码
 * @return string
 */
function compressHtml($html_source) {
	return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</", "//", "'/\*[^*]*\*/'", "/\r\n/", "/\n/", "/\t/", '/>[ ]+</'), array(">\\1<", '', '', '', '', '', '><'), $html_source)));
}
/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
	if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
		return true;
	} elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
		return true;
	}
	return false;
}
/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '') {
	//多行URL地址支持
	$url = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg)) {
		$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	}

	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header('Location: ' . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo ($msg);
		}
		exit();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0) {
			$str .= $msg;
		}

		exit($str);
	}
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
	$type = $type ? 1 : 0;
	static $ip = NULL;
	if ($ip !== NULL) {
		return $ip[$type];
	}

	if ($adv) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if (false !== $pos) {
				unset($arr[$pos]);
			}

			$ip = trim($arr[0]);
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u", ip2long($ip));
	$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}
/**
 * 记录用户搜索词
 * @param string $word 搜索词
 * @return void
 */
function logResult($word = '') {
	/*$content = date("Y/m/d h:i:s")." ".get_client_ip()." ".$word."\r\n";
		    $file = "./log/log.log";
	*/
}
