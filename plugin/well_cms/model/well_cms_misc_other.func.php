<?php

// hook model_well_cms_misc_other_start.php

// 返回CMS所有版块数据(包括频道)
function well_get_cms_list($forumlist)
{
    $cmslist = arrlist_cond_orderby($forumlist, array('well_type' => 1), array(), 1, 1000);

    return $cmslist;
}

// 返回CMS版块数据(仅列表)
function well_get_cms_forumlist($forumlist)
{
    $cmslist = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_forum_type' => 0), array(), 1, 1000);

    return $cmslist;
}

// 返回CMS频道数据
function well_get_cms_channellist($forumlist)
{
    $channellist = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_forum_type' => 1), array(), 1, 1000);

    return $channellist;
}

// 返回所有频道数据
function well_get_channellist($forumlist)
{
    $channellist = arrlist_cond_orderby($forumlist, array('well_forum_type' => 1), array(), 1, 1000);
    $fidarr = arrlist_key_values($channellist, 'fid', 'name');
    $arr = array('0' => lang('well_top_column'));
    foreach ($fidarr as $key => $v) {
        $arr[$key] = $v;
    }
    return $arr;
}

// 返回BBS所有版块数据(包括频道)
function well_get_bbs_list($forumlist)
{
    $bbslist = arrlist_cond_orderby($forumlist, array('well_type' => 0), array(), 1, 1000);

    return $bbslist;
}

// 返回BBS版块数据(仅列表) 尚未开放bbs频道功能
function well_get_bbs_forumlist($forumlist)
{
    $bbslist = arrlist_cond_orderby($forumlist, array('well_type' => 0, 'well_forum_type' => 1), array(), 1, 1000);

    return $bbslist;
}

// 返回BBS频道数据
function well_get_bbs_channellist($forumlist)
{
    $channellist = arrlist_cond_orderby($forumlist, array('well_type' => 0, 'well_forum_type' => 1), array(), 1, 1000);

    return $channellist;
}

// 返回BBS频道数据
function well_get_fidarr($forumlist)
{
    $fidarr = arrlist_key_values($forumlist, 'fid', 'name');
    $arr = array('0' => lang('well_top_column'));
    foreach ($fidarr as $key => $v) {
        $arr[$key] = $v;
    }

    return $arr;
}

// check plugin installation
function well_check_plugin($dir, $file = NULL, $return = FALSE)
{
    $r = well_get_plugin_info($dir);
    if (empty($r)) return FALSE;

    $destpath = APP_PATH . 'plugin/' . $dir . '/';

    if ($file) {
        $getfile = $destpath . $file;
        $str = file_get_contents($getfile);
        return $return ? htmlspecialchars($str) : $str;
    } else {
        if ($r['installed'] && $r['enable']) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

// get plugin info
function well_get_plugin_info($dir)
{
    $destpath = APP_PATH . 'plugin/' . $dir . '/';
    if (!file_exists($destpath)) return FALSE;

    $conffile = $destpath . 'conf.json';
    $r = xn_json_decode(file_get_contents($conffile));
    return $r;
}

// CMS nav array
function well_cms_nav_arr()
{
    global $forumlist;
    $cmslist = arrlist_cond_orderby($forumlist, array('well_type' => 1), array(), $page = 1, $pagesize = 1000);

    return well_cms_get_category_tree($cmslist);
}

// get category (tree structure)
function well_cms_get_category_tree($arr)
{
    $_arr = array();
    // 按fid排序数组
    foreach ($arr as $v) $_arr[$v['fid']] = $v;

    // 格式化为树状结构 (会舍弃不合格的结构)
    foreach ($_arr as $v) {
        // 按照上级pid格式化 归属子栏目到上级栏目
        if ($v['well_fup']) {
            $_arr[$v['well_fup']]['son'][$v['fid']] = $_arr[$v['fid']];
            unset($_arr[$v['fid']]);
        }
    }

    foreach ($_arr as $v) {
        $_arr[$v['fid']] = $v;
    }

    return $_arr;
}

function well_software_info()
{
    $pre = well_rand_str(5) . '_';
    $_conf = $_SERVER['well_conf'];

    if (!empty($_conf['name']) && !empty($_conf['version'])) {
        return $_conf;
    }

    $software = well_get_plugin_info('well_cms');
    $_conf['name'] = $software['name'];
    $_conf['version'] = $software['version'];
    //file_replace_var(APP_PATH . './plugin/well_cms/conf/conf.php', $_conf);
    well_cms_conf_write($_conf);

    $conf = $_SERVER['conf'];
    $memcached = $conf['cache']['memcached'];
    $redis = $conf['cache']['redis'];
    $xcache = $conf['cache']['xcache'];
    $yac = $conf['cache']['yac'];
    $apc = $conf['cache']['apc'];
    $write = 0;
    if ($memcached['cachepre'] == 'bbs_') {
        $conf['cache']['memcached']['cachepre'] = $pre;
        $write = 1;
    }

    if ($redis['cachepre'] == 'bbs_') {
        $conf['cache']['redis']['cachepre'] = $pre;
        $write = 1;
    }

    if ($xcache['cachepre'] == 'bbs_') {
        $conf['cache']['xcache']['cachepre'] = $pre;
        $write = 1;
    }

    if ($yac['cachepre'] == 'bbs_') {
        $conf['cache']['yac']['cachepre'] = $pre;
        $write = 1;
    }

    if ($apc['cachepre'] == 'bbs_') {
        $conf['cache']['apc']['cachepre'] = $pre;
        $write = 1;
    }

    if ($write) {
        $conf['tmp_path'] = './tmp/';
        $conf['log_path'] = './log/';
        $conf['view_url'] = 'view/';
        $conf['upload_url'] = 'upload/';
        $conf['upload_path'] = './upload/';
        file_replace_var(APP_PATH . 'conf/conf.php', $conf);
    }

    return $_conf;
}

// replace xn json placeholder
function well_json_encode($arr)
{
    $search = array(" ", "　", "\n", "\r", "\t");
    $replace = array('', '', '', '', '');
    return str_replace($search, $replace, xn_json_encode($arr));
}

// 0:pc 1:wechat 2:mobile 3:pad
function well_detect_device()
{
    $agent = _SERVER('HTTP_USER_AGENT');

    if (strpos($agent, 'MicroMessenger') !== false) {

        return 1;//微信

    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap") || stripos($agent, 'phone') || stripos($agent, 'mobile') || strpos($agent, 'ipod'))) {

        return 2;// 手机

    } elseif (strpos($agent, 'pad') !== FALSE) {

        return 3;//pad;

    }

    return 0;
}

function well_check_forumlist_index($forumlist)
{
    foreach ($forumlist as $_forum) {
        if (!empty($_forum['well_type'])) {
            return TRUE;
        }
    }
    return FALSE;
}

function well_check_forumlist_channel($well_fup, $forumlist)
{
    $arr = arrlist_cond_orderby($forumlist, array('well_fup' => $well_fup), array(), 1, 1000);

    if (!empty($arr)) return TRUE;

    return FALSE;
}

//install demo data;
function well_install_demo()
{
    // 安装
    $demo_sql = APP_PATH . './plugin/well_cms/data/install_demo.sql';
    $demo_attach = APP_PATH . './plugin/well_cms/data/attach/';
    if (file_exists($demo_sql)) {
        $_conf = $_SERVER['well_conf'];
        well_install_mysql_file($demo_sql);
        // 复制附件
        $demo_attach AND copy_recusive($demo_attach, APP_PATH . './upload/attach/');
        $_conf['installed'] = 1;
        well_cms_conf_write($_conf);
    }
}

//unstall demo data;
function well_unstall_demo()
{
    // 清理测试附件
    $attachlist = attach__find(array(), array(), 1, 1000);
    if ($attachlist) {
        foreach ($attachlist as $file) {
            $path = APP_PATH . 'upload/attach/';
            if ($file['isimage']) {
                $path_file = $path . $file['filename'];
                file_exists($path_file) AND unlink($path_file);
            }
        }
    }

    // 清理数据
    $demo_sql = APP_PATH . './plugin/well_cms/data/unstall_demo.sql';
    file_exists($demo_sql) AND well_install_mysql_file($demo_sql);
    $_conf = $_SERVER['well_conf'];
    $_conf['installed'] = 0;
    well_cms_conf_write($_conf);
    //well_cms_conf_write(array('installed' => 0));
}

// well_install_mysql_file(INSTALL_PATH.'install.sql');
function well_install_mysql_file($sqlfile)
{
    global $errno, $errstr;
    $s = file_get_contents($sqlfile);
    $s = str_replace(";\r\n", ";\n", $s);
    $arr = explode(";\n", $s);
    foreach ($arr as $sql) {
        $sql = trim($sql);
        if (empty($sql)) continue;
        db_exec($sql) === FALSE AND message(-1, "sql: $sql, errno: $errno, errstr: $errstr");
    }
}

// random string, no number
function well_rand_str($length)
{
    $str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm';
    return substr(str_shuffle($str), 26, $length);
}

// filter keyword
function well_filter_keyword($keyword, $type, &$error)
{
    $well_conf = setting_get('well_conf');
    $filter = array_value($well_conf, 'filter');
    $arr = array_value($filter, $type);
    $enable = array_value($arr, 'enable');
    $wordarr = array_value($arr, 'keyword');

    if ($enable == 0 || empty($wordarr)) return FALSE;

    foreach ($wordarr as $_keyword) {
        $r = strpos(strtolower($keyword), strtolower($_keyword));
        if ($r !== FALSE) {
            $error = $_keyword;
            return TRUE;
        }
    }
    return FALSE;
}

function well_filter_html($text)
{
    $well_conf = setting_get('well_conf');
    $filter = array_value($well_conf, 'filter');
    $arr = array_value($filter, 'content');
    $html_enable = array_value($arr, 'html_enable');
    $html_tag = array_value($arr, 'html_tag');

    if ($html_enable == 0 || empty($html_tag)) return TRUE;
    $html_tag = htmlspecialchars_decode($html_tag);

    $text = trim($text);
    $text = stripslashes($text);
    // 过滤动态代码
    $text = preg_replace('/<\?|\?' . '>/', '', $text);
    $text = preg_replace('@<script(.*?)</script>@is', '', $text);
    $text = preg_replace('@<iframe(.*?)</iframe>@is', '', $text);
    $text = preg_replace('@<style(.*?)</style>@is', '', $text);
    $text = strip_tags($text, "$html_tag"); // 需要保留的字符在后台设置
    $text = str_replace(array("\r\n", "\r", "\n", '  ', '   ', '    ', '	'), '', $text);
    //$text = preg_replace('/\s+/', '', $text);//空白区域 会过滤图片等
    //$text = preg_replace("@<(.*?)>@is", "", $text);
    // 过滤所有的style
    $text = preg_replace("/style=.+?['|\"]/i", '', $text);
    // 过滤所有的class
    $text = preg_replace("/class=.+?['|\"]/i", '', $text);
    // 获取img= 过滤标签中其他属性
    $text = preg_replace("/<img\s*src=(\"|\')(.*?)\\1[^>]*>/is", '<img src="$2" />', $text);

    return $text;
}

// 0 o'clock on the day
function well_clock_zero()
{
    return strtotime(date('Ymd'));
}

// 24 o'clock on the day
function well_clock_twenty_four()
{
    return strtotime(date('Ymd')) + 86400;
}

// expired at 8 a.m.
function well_cache_eight_expired()
{
    $time = $_SERVER['time'];
    // 24 - time + 3600 * 8
    $life = well_clock_twenty_four() - $time + 28800;
    return $life;
}

// hook model_well_cms_misc_other_end.php

?>