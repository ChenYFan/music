<?php
class msg {
    public function get() {
        header('Content-Type:application/json; charset=utf-8');
        $Referrer = isset($_POST['referrer']) ? $_POST['referrer'] : '';
        $UA = $_SERVER['HTTP_USER_AGENT'];
        $Msg = [];
        array_push($Msg, ['text' => '<a href="https://blog.cyfan.ga/捐款吧/">我们拒绝广告，但欢迎捐助！</a>']);
        if (strstr($Referrer, 'www.baidu.com')) {
            array_push($Msg, ['text' => '欢迎通过百度搜索访问的你！', 'style' => '', 'time' => 3000]);
        }
		if (strstr($Referrer, 'tools.cyfan.ga')) {
            array_push($Msg, ['text' => '你居然进入了人机验证！', 'style' => '', 'time' => 3000]);
        }
        if (strstr($UA, 'CoolMarket')) {
            array_push($Msg, ['text' => '欢迎通过酷安访问的你！', 'style' => '', 'time' => 3000]);
        }
        array_push($Msg, ['text' => '2019年8月9日 我们强制更新音乐搜索！它又可以用了！', 'style' => '', 'time' => 3000]);
        response($Msg, 200, '');
        exit();
    }
}