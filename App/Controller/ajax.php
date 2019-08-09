<?php
class ajax {
    public function search($param) {
        $Token = $param['token'];
        $Text = $param['text'];
        $Page = $param['page'];
        $Type = $param['type'];
        if ($Token !== md5('text/' . $Text . '/page/' . $Page . '/type/' . $Type . 'tool.liumingye.cn')) {
            send_http_status(403, TRUE);
            exit();
        }
        $Text = base64_decode(str_replace('*', '/', $param['text']));
        /*
        MyFreeMp3
        */
        function SearchMyFreeMp3($Text, $Page) {
            $Data = curl_request('https://my-free-mp3s.com/api/search.php?callback=jQuery2130753441720219949_1551201410793', array('q' => urldecode($Text), 'page' => ($Page - 1)), array('X-Requested-With:XMLHttpRequest', 'Referer:https://my-free-mp3s.com/mp3juices'));
            $Data = jsonp_decode($Data, true);
            $Data = $Data['response'];
            $List = ['list' => []];
            function encode($input) {
                $Map = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', '1', '2', '3'];
                $length = count($Map);
                $encoded = "";
                if ($input === 0) {
                    return $Map[0];
                }
                if ($input < 0) {
                    $input*= - 1;
                    $encoded.= "-";
                }
                while ($input > 0) {
                    $val = floor($input % $length);
                    $input = floor($input / $length);
                    $encoded.= $Map[$val];
                }
                return $encoded;
            }
            if (count($Data) != 0) {
                foreach ($Data as $key => $value) {
                    if (is_array($value) === false) {
                        continue;
                    }
                    $temp['name'] = $value['title'];
                    $temp['artist'] = $value['artist'];
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/myfreemp3/mid/' . $value['id'];
                    $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    if ($value['is_hq'] === true) {
                        $temp['url_320'] = 'https://sendto.club/' . encode($value['owner_id']) . ':' . encode($value['id']);
                        $temp['url'] = $temp['url_320'];
                    } else {
                        $temp['url_128'] = 'https://sendto.club/' . encode($value['owner_id']) . ':' . encode($value['id']);
                        $temp['url'] = $temp['url_128'];
                    }
                    array_push($List['list'], $temp);
                    unset($temp);
                }
            }
            $List['more'] = '1';
            return $List;
        }
        /*
        音乐搜索器
        */
        function SearchMusic($Url, $Text, $Page, $Type) {
            $Data = curl_request($Url, array('input' => urldecode($Text), 'filter' => 'name', 'type' => $Type, 'page' => $Page), array('X-Requested-With:XMLHttpRequest', 'Referer:' . $Url));
			$Data = json_decode($Data, true);
            if ($Data['code'] !== 200) {
                if (!$Data['error']) {
                    $Data['error'] = '获取数据超时，请重试！';
                }
                response('', $Data['code'], $Data['error']);
                exit();
            }
            $Data = $Data['data'];
            $List = ['list' => []];
            if (count($Data) != 0) {
                foreach ($Data as $key => $value) {
                    $temp['name'] = $value['title'];
                    $temp['artist'] = $value['author'];
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/' . $Type . '/mid/' . $value['songid'];
                    if ($value['pic']) {
                        $temp['cover'] = $value['pic'];
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['url_128'] = $value['url'];
                    $temp['url'] = $temp['url_128'];
                    array_push($List['list'], $temp);
                    unset($temp);
                }
            }
			$List['more'] = '2';
            return $List;
        }
        /*
        --------
        全民K歌
        --------
        */
        function SearchQmkgSong($id) {
            $Data = curl_request('http://cgi.kg.qq.com/fcgi-bin/kg_ugc_getdetail?format=json&outCharset=utf-8&v=4&shareid=' . $id);
            $Data = json_decode($Data, true);
            if ($Data['code'] !== 0) {
                response('', 400, $Data['message']);
                exit();
            }
            $Data = $Data['data'];
            $List = ['list' => []];
            if (count($Data) != 0) {
                $temp['name'] = $Data['song_name'];
                $temp['artist'] = $Data['kg_nick'];
                $temp['lrc'] = SITE_URL . 'lrc/get/type/kg/mid/' . $Data['ksong_mid'];
                $temp['cover'] = $Data['cover'];
                $temp['url_128'] = $Data['playurl'];
                if ($temp['url_128'] === '') {
                    $temp['url_128'] = $Data['playurl_video'];
                }
                $temp['url'] = $temp['url_128'];
                array_push($List['list'], $temp);
                unset($temp);
            }
            return $List;
        }
        /*
        -----------
        网易云音乐
        -----------
        */
        function SearchNeteaseList($text, $page) {
            $Data = curl_request('http://music.163.com/api/cloudsearch/pc?s=' . $text . '&type=1&limit=20&offset=' . (($page - 1) * 20));
            $Data = json_decode($Data, true);
            $ListData = $Data['result']['songs'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $key => $value) {
                    $temp['name'] = $value['name'];
                    foreach ($value['ar'] as $val) {
                        if ($temp['artist'] === null) {
                            $temp['artist'] = $val['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $val['name'];
                        }
                    }
                    if ($value['al']['picUrl']) {
                        $temp['cover'] = $value['al']['picUrl'];
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/netease/mid/' . $value['id'];
                    $temp['url_128'] = 'https://api.imjad.cn/cloudmusic/?type=song&br=320000&raw=true&id=' . $value['id'];
                    $temp['url'] = $temp['url_128'];
                    array_push($List['list'], $temp);
                    unset($temp);
                }
                if ($Data['result']['songCount'] / ($page * 20) > 1) {
                    $List['more'] = '1';
                } else {
                    $List['more'] = '0';
                }
            }
            return $List;
        }
        function SearchNeteasePlaylist($id) {
            $Data = curl_request('http://musicapi.leanapp.cn/playlist/detail?id=' . $id);
            $Data = json_decode($Data, true);
            $ListData = $Data['playlist']['tracks'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $value) {
                    $temp['name'] = $value['name'];
                    foreach ($value['ar'] as $v) {
                        if ($temp['artist'] === null) {
                            $temp['artist'] = $v['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $v['name'];
                        }
                    }
                    $temp['cover'] = $value['al']['picUrl'];
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/netease/mid/' . $value['id'];
                    $temp['url_128'] = 'https://api.imjad.cn/cloudmusic/?type=song&br=320000&raw=true&id=' . $value['id'];
                    $temp['url'] = $temp['url_128'];
                    array_push($List['list'], $temp);
                    unset($temp);
                }
            }
            return $List;
        }
        function SearchNeteaseSong($id) {
            $Data = curl_request('http://music.163.com/api/song/detail?id=' . $id . '&ids=[' . $id . ']');
            $Data = json_decode($Data, true);
            if ($Data['code'] !== 200) {
                response('', 400, $Data['msg']);
                exit();
            }
            $Data = $Data['songs'][0];
            $List = ['list' => []];
            if (count($Data) != 0) {
                $temp['name'] = $Data['name'];
                foreach ($Data['artists'] as $val) {
                    if ($temp['artist'] === null) {
                        $temp['artist'] = $val['name'];
                    } else {
                        $temp['artist'] = $temp['artist'] . "," . $val['name'];
                    }
                }
                $temp['cover'] = $Data['album']['picUrl'];
                $temp['lrc'] = SITE_URL . 'lrc/get/type/netease/mid/' . $id;
                $temp['url_128'] = 'https://api.imjad.cn/cloudmusic/?type=song&br=320000&raw=true&id=' . $Data['id'];
                $temp['url'] = $temp['url_128'];
                array_push($List['list'], $temp);
                unset($temp);
            }
            return $List;
        }
        /*
        --------
        QQ音乐
        --------
        */
        function SearchQQList($text, $page) {
            $Data = curl_request('http://c.y.qq.com/soso/fcgi-bin/client_search_cp?cr=1&p=' . $page . '&n=20&w=' . $text . '&format=json');
            $Data = json_decode($Data, true);
            $ListData = $Data['data']['song']['list'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $key => $value) {
                    $temp['name'] = $value['songname'];
                    foreach ($value['singer'] as $val) {
                        if (!isset($temp['artist'])) {
                            $temp['artist'] = $val['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $val['name'];
                        }
                    }
                    if ($value['albumid'] != "0") {
                        $temp['cover'] = 'http://y.gtimg.cn/music/photo_new/T002R800x800M000' . $value['albummid'] . '.jpg';
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/qq/mid/' . $value['songmid'];
                    if ($value['sizeape'] != "0") {
                        $temp['url_ape'] = 'http://mobileoc.music.tc.qq.com/A000' . $value['media_mid'] . '.ape?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_ape'];
                    }
                    if ($value['sizeflac'] != "0") {
                        $temp['url_flac'] = 'http://mobileoc.music.tc.qq.com/F000' . $value['media_mid'] . '.flac?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_flac'];
                    }
                    if ($value['size320'] != "0") {
                        $temp['url_320'] = 'http://mobileoc.music.tc.qq.com/M800' . $value['media_mid'] . '.mp3?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_320'];
                    }
                    if ($value['size128'] != "0") {
                        $temp['url_128'] = 'http://mobileoc.music.tc.qq.com/M500' . $value['media_mid'] . '.mp3?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_128'];
                    }
                    if ($value['sizeogg'] != "0") {
                        $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['media_mid'] . '.m4a?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_m4a'];
                    }
                    if (!isset($temp['url'])) {
                        $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['media_mid'] . '.m4a?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_m4a'];
                    }
                    array_push($List['list'], $temp);
                    unset($temp);
                }
                if ($Data['data']['song']['totalnum'] / ($page * 20) > 1) {
                    $List['more'] = '1';
                } else {
                    $List['more'] = '0';
                }
            }
            return $List;
        }
        function SearchSong($id, $type) {
            if ($type === 1) {
                $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg?songid=' . $id . '&format=json');
            } elseif ($type === 2) {
                $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg?songmid=' . $id . '&format=json');
            }
            $Data = json_decode($Data, true);
            $Data = $Data['data'][0];
            $List = ['list' => []];
            if (count($Data) != 0) {
                $temp['name'] = $Data['title'];
                foreach ($Data['singer'] as $val) {
                    if ($temp['artist'] === null) {
                        $temp['artist'] = $val['name'];
                    } else {
                        $temp['artist'] = $temp['artist'] . "," . $val['name'];
                    }
                }
                if ($Data['album']['id'] != "0") {
                    $temp['cover'] = 'http://y.gtimg.cn/music/photo_new/T002R800x800M000' . $Data['album']['mid'] . '.jpg';
                } else {
                    $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                }
                $temp['lrc'] = SITE_URL . 'lrc/get/type/qq/mid/' . $Data['mid'];
                if ($Data['file']['size_ape'] != "0") {
                    $temp['url_ape'] = 'http://mobileoc.music.tc.qq.com/A000' . $Data['file']['media_mid'] . '.ape?guid=0&uin=0&fromtag=8';
                    $temp['url'] = $temp['url_ape'];
                }
                if ($Data['file']['size_flac'] != "0") {
                    $temp['url_flac'] = 'http://mobileoc.music.tc.qq.com/F000' . $Data['file']['media_mid'] . '.flac?guid=0&uin=0&fromtag=53';
                    $temp['url'] = $temp['url_flac'];
                }
                if ($Data['file']['size_320mp3'] != "0") {
                    $temp['url_320'] = 'http://mobileoc.music.tc.qq.com/M800' . $Data['file']['media_mid'] . '.mp3?guid=0&uin=0&fromtag=8';
                    $temp['url'] = $temp['url_320'];
                }
                if ($Data['file']['size_128mp3'] != "0") {
                    $temp['url_128'] = 'http://mobileoc.music.tc.qq.com/M500' . $Data['file']['media_mid'] . '.mp3?guid=0&uin=0&fromtag=53';
                    $temp['url'] = $temp['url_128'];
                }
                if ($Data['file']['size_192ogg'] != "0") {
                    $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $Data['file']['media_mid'] . '.m4a?guid=0&uin=0&fromtag=53';
                    $temp['url'] = $temp['url_m4a'];
                }
                if (!$temp['url']) {
                    $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['media_mid'] . '.m4a?guid=0&uin=0&fromtag=53';
                    $temp['url'] = $temp['url_m4a'];
                }
                array_push($List['list'], $temp);
                unset($temp);
            }
            $List['more'] = '0';
            return $List;
        }
        function SearchAlbum($id, $type, $page) {
            if ($type === 1) {
                $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_v8_album_info_cp.fcg?albumid=' . $id . '&format=json&song_num=20&song_begin=' . (($page - 1) * 20));
            } elseif ($type === 2) {
                $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_v8_album_info_cp.fcg?albummid=' . $id . '&format=json&song_num=20&song_begin=' . (($page - 1) * 20));
            }
            $Data = json_decode($Data, true);
            $ListData = $Data['data']['list'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $value) {
                    $temp['name'] = $value['songname'];
                    foreach ($value['singer'] as $val) {
                        if ($temp['artist'] === null) {
                            $temp['artist'] = $val['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $val['name'];
                        }
                    }
                    if ($value['albumid'] != "0") {
                        $temp['cover'] = 'http://y.gtimg.cn/music/photo_new/T002R800x800M000' . $value['albummid'] . '.jpg';
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/qq/mid/' . $value['songmid'];
                    if ($value['sizeape'] != "0") {
                        $temp['url_ape'] = 'http://mobileoc.music.tc.qq.com/A000' . $value['strMediaMid'] . '.ape?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_ape'];
                    }
                    if ($value['sizeflac'] != "0") {
                        $temp['url_flac'] = 'http://mobileoc.music.tc.qq.com/F000' . $value['strMediaMid'] . '.flac?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_flac'];
                    }
                    if ($value['size320'] != "0") {
                        $temp['url_320'] = 'http://mobileoc.music.tc.qq.com/M800' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_320'];
                    }
                    if ($value['size128'] != "0") {
                        $temp['url_128'] = 'http://mobileoc.music.tc.qq.com/M500' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_128'];
                    }
                    if ($value['sizeogg'] != "0") {
                        $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['strMediaMid'] . '.m4a?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_m4a'];
                    }
                    array_push($List['list'], $temp);
                    unset($temp);
                }
                if ($Data['data']['total'] / ($page * 20) > 1) {
                    $List['more'] = '1';
                } else {
                    $List['more'] = '0';
                }
            }
            return $List;
        }
        function SearchSinger($id, $type, $page) {
            if ($type === 1) {
                $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_v8_singer_track_cp.fcg?singerid=' . $id . '&order=listen&begin=' . (($page - 1) * 20) . '&num=20&songstatus=1&format=json');
            } elseif ($type === 2) {
                $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_v8_singer_track_cp.fcg?singermid=' . $id . '&order=listen&begin=' . (($page - 1) * 20) . '&num=20&songstatus=1&format=json');
            }
            $Data = json_decode($Data, true);
            $ListData = $Data['data']['list'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $value) {
                    $value = $value['musicData'];
                    $temp['name'] = $value['songname'];
                    foreach ($value['singer'] as $val) {
                        if ($temp['artist'] === null) {
                            $temp['artist'] = $val['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $val['name'];
                        }
                    }
                    if ($value['albumid'] != "0") {
                        $temp['cover'] = 'http://y.gtimg.cn/music/photo_new/T002R800x800M000' . $value['albummid'] . '.jpg';
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/qq/mid/' . $value['songmid'];
                    if ($value['sizeape'] != "0") {
                        $temp['url_ape'] = 'http://mobileoc.music.tc.qq.com/A000' . $value['strMediaMid'] . '.ape?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_ape'];
                    }
                    if ($value['sizeflac'] != "0") {
                        $temp['url_flac'] = 'http://mobileoc.music.tc.qq.com/F000' . $value['strMediaMid'] . '.flac?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_flac'];
                    }
                    if ($value['size320'] != "0") {
                        $temp['url_320'] = 'http://mobileoc.music.tc.qq.com/M800' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_320'];
                    }
                    if ($value['size128'] != "0") {
                        $temp['url_128'] = 'http://mobileoc.music.tc.qq.com/M500' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_128'];
                    }
                    if ($value['sizeogg'] != "0") {
                        $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['strMediaMid'] . '.m4a?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_m4a'];
                    }
                    array_push($List['list'], $temp);
                    unset($temp);
                }
                if ($Data['data']['total'] / ($page * 20) > 1) {
                    $List['more'] = '1';
                } else {
                    $List['more'] = '0';
                }
            }
            return $List;
        }
        function SearchToplist($id, $page) {
            $Data = curl_request('https://c.y.qq.com/v8/fcg-bin/fcg_v8_toplist_cp.fcg?topid=' . $id . '&song_begin=' . (($page - 1) * 20) . '&song_num=20&format=json');
            $Data = json_decode($Data, true);
            $ListData = $Data['songlist'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $value) {
                    $value = $value['data'];
                    $temp['name'] = $value['songname'];
                    foreach ($value['singer'] as $val) {
                        if ($temp['artist'] === null) {
                            $temp['artist'] = $val['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $val['name'];
                        }
                    }
                    if ($value['albumid'] != "0") {
                        $temp['cover'] = 'http://y.gtimg.cn/music/photo_new/T002R800x800M000' . $value['albummid'] . '.jpg';
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/qq/mid/' . $value['songmid'];
                    if ($value['sizeape'] != "0") {
                        $temp['url_ape'] = 'http://mobileoc.music.tc.qq.com/A000' . $value['strMediaMid'] . '.ape?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_ape'];
                    }
                    if ($value['sizeflac'] != "0") {
                        $temp['url_flac'] = 'http://mobileoc.music.tc.qq.com/F000' . $value['strMediaMid'] . '.flac?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_flac'];
                    }
                    if ($value['size320'] != "0") {
                        $temp['url_320'] = 'http://mobileoc.music.tc.qq.com/M800' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_320'];
                    }
                    if ($value['size128'] != "0") {
                        $temp['url_128'] = 'http://mobileoc.music.tc.qq.com/M500' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_128'];
                    }
                    if ($value['sizeogg'] != "0") {
                        $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['strMediaMid'] . '.m4a?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_m4a'];
                    }
                    array_push($List['list'], $temp);
                    unset($temp);
                }
                if ($Data['total_song_num'] / ($page * 20) > 1) {
                    $List['more'] = '1';
                } else {
                    $List['more'] = '0';
                }
            }
            return $List;
        }
        function SearchPlaylist($id) {
            $Data = curl_request('https://c.y.qq.com/qzone/fcg-bin/fcg_ucc_getcdinfo_byids_cp.fcg?type=1&utf8=1&disstid=' . $id . '&format=json');
            $Data = json_decode($Data, true);
            $ListData = $Data['cdlist'][0]['songlist'];
            $List = ['list' => []];
            if (count($ListData) != 0) {
                foreach ($ListData as $value) {
                    $temp['name'] = $value['songname'];
                    foreach ($value['singer'] as $val) {
                        if ($temp['artist'] === null) {
                            $temp['artist'] = $val['name'];
                        } else {
                            $temp['artist'] = $temp['artist'] . "," . $val['name'];
                        }
                    }
                    if ($value['albumid'] != "0") {
                        $temp['cover'] = 'http://y.gtimg.cn/music/photo_new/T002R800x800M000' . $value['albummid'] . '.jpg';
                    } else {
                        $temp['cover'] = 'https://i.loli.net/2018/06/29/5b350a136074a.png';
                    }
                    $temp['lrc'] = SITE_URL . 'lrc/get/type/qq/mid/' . $value['songmid'];
                    if ($value['sizeape'] != "0") {
                        $temp['url_ape'] = 'http://mobileoc.music.tc.qq.com/A000' . $value['strMediaMid'] . '.ape?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_ape'];
                    }
                    if ($value['sizeflac'] != "0") {
                        $temp['url_flac'] = 'http://mobileoc.music.tc.qq.com/F000' . $value['strMediaMid'] . '.flac?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_flac'];
                    }
                    if ($value['size320'] != "0") {
                        $temp['url_320'] = 'http://mobileoc.music.tc.qq.com/M800' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=8';
                        $temp['url'] = $temp['url_320'];
                    }
                    if ($value['size128'] != "0") {
                        $temp['url_128'] = 'http://mobileoc.music.tc.qq.com/M500' . $value['strMediaMid'] . '.mp3?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_128'];
                    }
                    if ($value['sizeogg'] != "0") {
                        $temp['url_m4a'] = 'http://mobileoc.music.tc.qq.com/C400' . $value['strMediaMid'] . '.m4a?guid=0&uin=0&fromtag=53';
                        $temp['url'] = $temp['url_m4a'];
                    }
                    array_push($List['list'], $temp);
                    unset($temp);
                }
            }
            return $List;
        }
        /*
        QQ音乐
        */
        preg_match('/y\.qq\.com\/n\/yqq\/song\/([a-zA-Z0-9]+)(_num|).html/i', $Text, $match_Song);
        preg_match('/y\.qq\.com\/v8\/playsong.html?(.*?)songmid=([a-zA-Z0-9]+)/i', $Text, $match_Song_Wap);
        preg_match('/y\.qq\.com\/n\/yqq\/album\/([a-zA-Z0-9]+)(_num|).html/i', $Text, $match_Album);
        preg_match('/y\.qq\.com\/w\/album.html?(.*?)[albumid|albummid]=([a-zA-Z0-9]+)/i', $Text, $match_Album_Wap);
        preg_match('/y\.qq\.com\/n\/yqq\/playsquare\/([a-zA-Z0-9]+).html/i', $Text, $match_Playsquare);
        preg_match('/y\.qq\.com\/n\/yqq\/playlist\/([a-zA-Z0-9]+).html/i', $Text, $match_Playlist);
        preg_match('/y\.qq\.com\/w\/taoge.html?(.*?)id=([a-zA-Z0-9]+)/i', $Text, $match_Playlist_Wap);
        preg_match('/y\.qq\.com\/n\/yqq\/singer\/([a-zA-Z0-9]+)(_num|).html/i', $Text, $match_Singer);
        preg_match('/y\.qq\.com\/w\/singer.html?(.*?)singerid=([a-zA-Z0-9]+)/i', $Text, $match_Singer_Wap);
        preg_match('/y\.qq\.com\/n\/yqq\/toplist\/([a-zA-Z0-9]+).html/i', $Text, $match_Toplist);
        preg_match('/y\.qq\.com\/w\/toplist.html?(.*?)id=([a-zA-Z0-9]+)/i', $Text, $match_Toplist_Wap);
        /*
        网易云
        */
        preg_match('/music\.163\.com\/(#\/|)song(\?id=|\/)([0-9]+)/i', $Text, $match_Netease_Song);
        preg_match('/music\.163\.com\/(#\/|)(my\/m\/music\/|)playlist(\?id=|\/)([0-9]+)/i', $Text, $match_Netease_Playlist);
        /*
        全民K歌
        */
        preg_match('/node\.kg\.qq\.com\/(play|share\.html)\?s=([a-zA-Z0-9_]+)/i', $Text, $match_Qmkg_Song);
        //preg_match('/node\.kg\.qq\.com\/personal\?uid=([a-zA-Z0-9]+)/i', $Text, $match_Qmkg_Singer);
        $Text = urlencode($Text);
        /*
        QQ音乐
        */
        if (!empty($match_Song)) {
            $id = $match_Song[1];
            if (is_numeric($id)) {
                $List = SearchSong($id, 1); //ID
                
            } else {
                $List = SearchSong($id, 2); //MID
                
            }
        } elseif (!empty($match_Song_Wap)) {
            $id = $match_Song_Wap[2];
            $List = SearchSong($id, 2); //MID
            
        } elseif (!empty($match_Album)) {
            $id = $match_Album[1];
            if (is_numeric($id)) {
                $List = SearchAlbum($id, 1, $Page); //ID
                
            } else {
                $List = SearchAlbum($id, 2, $Page); //MID
                
            }
        } elseif (!empty($match_Album_Wap)) {
            $id = $match_Album_Wap[2];
            if (is_numeric($id)) {
                $List = SearchAlbum($id, 1, $Page); //ID
                
            } else {
                $List = SearchAlbum($id, 2, $Page); //MID
                
            }
        } elseif (!empty($match_Playsquare)) {
            $id = $match_Playsquare[1];
            $List = SearchPlaylist($id);
        } elseif (!empty($match_Singer)) {
            $id = $match_Singer[1];
            if (is_numeric($id)) {
                $List = SearchSinger($id, 1, $Page); //ID
                
            } else {
                $List = SearchSinger($id, 2, $Page); //MID
                
            }
        } elseif (!empty($match_Singer_Wap)) {
            $id = $match_Singer_Wap[2];
            if (is_numeric($id)) {
                $List = SearchSinger($id, 1, $Page); //ID
                
            } else {
                $List = SearchSinger($id, 2, $Page); //MID
                
            }
        } elseif (!empty($match_Toplist)) {
            $id = $match_Toplist[1];
            $List = SearchToplist($id, $Page);
        } elseif (!empty($match_Toplist_Wap)) {
            $id = $match_Toplist_Wap[2];
            $List = SearchToplist($id, $Page);
        } elseif (!empty($match_Playlist)) {
            $id = $match_Playlist[1];
            $List = SearchPlaylist($id);
        } elseif (!empty($match_Playlist_Wap)) {
            $id = $match_Playlist_Wap[2];
            $List = SearchPlaylist($id);
            /*
            网易云单曲
            */
        } elseif (!empty($match_Netease_Song)) {
            $id = $match_Netease_Song[3];
            $List = SearchNeteaseSong($id);
        } elseif (!empty($match_Netease_Playlist)) {
            $id = $match_Netease_Playlist[4];
            $List = SearchNeteasePlaylist($id);
            /*
            全民K歌
            */
        } elseif (!empty($match_Qmkg_Song)) {
            $id = $match_Qmkg_Song[2];
            $List = SearchQmkgSong($id);
        } elseif (!empty($match_Qmkg_Singer)) {
            $id = $match_Qmkg_Singer[2];
            $List = SearchQmkgSinger($id);
            /*
            搜索音乐
            */
        } elseif ($Type === 'qq') {
            $List = SearchQQList($Text, $Page);
        } elseif ($Type === 'netease') {
            $List = SearchNeteaseList($Text, $Page);
        } elseif ($Type === 'kugou' || $Type === 'kuwo' || $Type === 'qingting' || $Type === 'ximalaya' || $Type === '5singyc' || $Type === '5singfc') {
            $List = SearchMusic('http://music.cccyun.cc/', $Text, $Page, $Type);
        } elseif ($Type === 'xiami' || $Type === 'baidu' || $Type === '1ting' || $Type === 'migu' || $Type === 'lizhi' || $Type === 'kg') {
            $List = SearchMusic('http://music.ifkdy.com/', $Text, $Page, $Type);
        } elseif ($Type === 'myfreemp3') {
            $List = SearchMyFreeMp3($Text, $Page);
        } else {
            $List = SearchQQList($Text, $Page);
        }
        response($List, 200, '');
        logResult('search ' . $Text);
        exit();
        // END END END END END END END END END END END END END END END END END END END END END END END END
        
    }
}
