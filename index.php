<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Max-Age: 3628800');
header('Cache-Control: max-age=3628800');
header('Etag: "20190225"');
ob_start();
// 版本信息
const APP_VERSION = '20190225';
// 系统常量定义
define('ROOT_PATH', __DIR__ . '/');
define('APP_PATH', ROOT_PATH . 'App/');
define('CORE_PATH', ROOT_PATH . 'Core/');
define('Public_PATH', ROOT_PATH . 'Public/');
// 系统信息
define('IS_CGI', (0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi')) ? 1 : 0);
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
if (!IS_CLI) {
    // 当前文件名
    if (!defined('_PHP_FILE_')) {
        if (IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp = explode('.php', $_SERVER['PHP_SELF']);
            define('_PHP_FILE_', rtrim(str_replace($_SERVER['HTTP_HOST'], '', $_temp[0] . '.php'), '/'));
        } else {
            define('_PHP_FILE_', rtrim($_SERVER['SCRIPT_NAME'], '/'));
        }
    }
    if (!defined('__ROOT__')) {
        $_root = rtrim(dirname(_PHP_FILE_), '/');
        define('__ROOT__', (($_root == '/' || $_root == '\\') ? '' : $_root));
    }
}
define('SITE_URL', 'http://' . $_SERVER['SERVER_NAME'] . __ROOT__ . '/');
// 加载核心函数
require CORE_PATH . 'common.php';
require CORE_PATH . 'router.php';
// HTTPS强制跳转到HTTP
if (is_ssl()) {
    redirect(SITE_URL);
}
$content = ob_get_contents();
ob_end_clean();
echo compressHtml($content) . '<!-- ' . APP_VERSION . ' -->';