<?php
class lrc {
    public function get($param) {
        $Mid = $param['mid'];
        $Type = $param['type'];
        if (!$Mid || !$Type) {
            send_http_status(403, TRUE);
            exit();
        }
        $Download = isset($param['download']) ? $param['download'] : '0';
        $Name = isset($param['name']) ? $param['name'] : $Mid . '.lrc';
        if ($Type === 'qq') {
            $Lrcdata = curl_request('https://c.y.qq.com/lyric/fcgi-bin/fcg_query_lyric_new.fcg?new_json=1&format=json&songmid=' . $Mid);
            $Lrcdata = jsonp_decode($Lrcdata);
            $Lrcdata = base64_decode($Lrcdata['lyric']);
            $Lrcdata = str_replace('[00:00:00]此歌曲为没有填词的纯音乐，请您欣赏', '[00:00.00]此歌曲为没有填词的纯音乐，请您欣赏', $Lrcdata);
        } elseif ($Type === 'netease') {
            $Lrcdata = curl_request('http://music.163.com/api/song/lyric?lv=1&id=' . $Mid);
            $Lrcdata = json_decode($Lrcdata, true);
            $Lrcdata = $Lrcdata['lrc']['lyric'];
        } elseif ($Type === 'kg') {
            $Lrcdata = curl_request('http://kg.qq.com/cgi/fcg_lyric?inCharset=utf8&outCharset=utf-8&format=json&v=4&ksongmid=' . $Mid);
            $Lrcdata = json_decode($Lrcdata, true);
            $Lrcdata = $Lrcdata['data']['lyric'];
        } elseif ($Type === 'kugou') {
            $Lrcdata = curl_request('http://m.kugou.com/app/i/krc.php?cmd=100&timelength=999999&hash=' . $Mid);
        } elseif ($Type === 'kuwo') {
            $Lrcdata = curl_request('http://m.kuwo.cn/newh5/singles/songinfoandlrc?musicId=' . $Mid);
            $Lrcdata = json_decode($Lrcdata, true);
            $Lrcdata = $Lrcdata['data']['lrclist'];
            if ($Lrcdata) {
                $lrc = '';
                foreach ($Lrcdata as $val) {
                    if ($val['time'] > 60) {
                        $time_exp = explode('.', round($val['time'] / 60, 4));
                        $minute = $time_exp[0] < 10 ? '0' . $time_exp[0] : $time_exp[0];
                        $sec = substr($time_exp[1], 0, 2) . '.' . substr($time_exp[1], 2, 2);
                        $time = '[' . $minute . ':' . $sec . ']';
                    } else {
                        $time = '[00:' . $val['time'] . ']';
                    }
                    $lrc.= $time . $val['lineLyric'] . "\n";
                }
                $Lrcdata = $lrc;
            }
        }
        if ($Download === '1' && $Name) {
            header('Content-type:application/lrc');
            header('Content-Disposition:attachment;filename="' . $Name . '"');
            if ($Lrcdata) {
                echo "[pr:该歌词由刘明野的工具箱(tool.liumingye.cn)提供]\r\n", $Lrcdata;
            } else {
                echo '[00:00.00]暂无歌词';
            }
            logResult('lrc ' . $Name);
        } else {
            header('Content-Type:text/html');
            if ($Lrcdata) {
                if ($Lrcdata === "纯音乐请欣赏") {
                    echo '[00:00.00]此歌曲为没有填词的纯音乐，请您欣赏';
                } else {
                    echo $Lrcdata;
                }
            } else {
                echo '[00:00.00]暂无歌词';
            }
        }
        exit();
    }
}