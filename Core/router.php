<?php if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    exit;
}
?>
<?php
define('MODULE_DIR', APP_PATH . 'Controller/');
define('VIEW_DIR', APP_PATH . 'View/');
$_DocumentPath = $_SERVER['DOCUMENT_ROOT'];
$_RequestUri = $_SERVER['REQUEST_URI'];
$_UrlPath = $_RequestUri;
$_FilePath = __FILE__;
$_AppPath = str_replace($_DocumentPath, '', $_FilePath);
$_AppPathArr = explode(DIRECTORY_SEPARATOR, $_AppPath);
for ($i = 0; $i < count($_AppPathArr); $i++) {
    $p = $_AppPathArr[$i];
    if ($p) {
        $_UrlPath = preg_replace('/^\/' . $p . '\//', '/', $_UrlPath, 1);
    }
}
$_UrlPath = preg_replace('/^\//', '', $_UrlPath, 1);
if (isset($_SERVER['QUERY_STRING'])) {
    $_UrlPath = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_UrlPath);
}
if (substr($_UrlPath, 0, 10) === "index.php/") {
    $_UrlPath = substr($_UrlPath, 10);
}
$ary_url = ['controller' => 'home', 'method' => 'index', 'pramers' => []];
$ary_se = explode('/', $_UrlPath);
$se_count = count($ary_se);
if ($se_count === 0 || $se_count === 1) {
    if ($_UrlPath !== '') {
        $ary_url['controller'] = basename($_UrlPath, ".php");
    }
    if ($ary_url['controller'] === "index" && $ary_url['method'] === "index") {
        $ary_url['controller'] = "home";
        $ary_url['method'] = "index";
    }
    $module_name = $ary_url['controller'];
    $module_file = VIEW_DIR . $module_name . '.php';
} elseif ($se_count > 1) {
    $ary_url['controller'] = $ary_se[0] != '' ? $ary_se[0] : 'home';
    $ary_url['method'] = (isset($ary_se[1]) && $ary_se[1] != '') ? $ary_se[1] : 'index';
    if (isset($ary_se[2]) && $ary_se[2]) {
        $count = count($ary_se);
        if ($count === 3 || ($count === 4 && $ary_se[3] === '')) {
            $ary_url['pramers'] = $ary_se[2];
        } else {
            $ary_url['pramers'] = [];
            for ($i = 2; $i < $count; $i = $i + 2) {
                if (isset($ary_se[$i + 1])) {
                    $ary_kv_hash = array(strtolower($ary_se[$i]) => $ary_se[$i + 1]);
                    $ary_url['pramers'] = array_merge($ary_url['pramers'], $ary_kv_hash);
                }
            }
        }
    }

    $module_name = $ary_url['controller'];
    $module_file = MODULE_DIR . $module_name . '.php';
}
$method_name = $ary_url['method'];
if (file_exists($module_file)) {
    require $module_file;
    $obj_module = new $module_name();
    if (!method_exists($obj_module, $method_name)) {
        send_http_status(404, TRUE);
        exit();
    } else {
        if (is_callable(array($obj_module, $method_name))) {
            $get_return = $obj_module->$method_name($ary_url['pramers']);
            if (!is_null($get_return)) {
                echo ($get_return);
            }
        } else {
            send_http_status(404, TRUE);
            exit();
        }
    }
} else {
    send_http_status(404, TRUE);
    exit();
}
?>
