<?php

// hook model_well_web_start.php

// 过滤不显示的版块数据
/*
 * 首页调用
 * 轮播图
 * 头条(轮播下)
 * 导读
 * 推荐(最右侧推荐标签上)
 * */
//--------------首页----------------
// 首页数据
function well_cms_web_index_data($forumlist)
{
    // hook model_well_cms_web_index_data_start.php

    if (empty($forumlist)) return array();

    $r = well_check_forumlist_index($forumlist);
    if ($r === FALSE) return array();

    $threadlist = cache_get('wellcms_index_threadlist');

    if ($threadlist === NULL) {

        $flag_thread = well_cms_index_flag_thread();

        $forum_thread = well_cms_index_forum_thread($forumlist);

        // hook model_well_cms_web_index_data_threadlist_start.php

        $threadlist = array();

        foreach ($forum_thread as $key => $thread) {
            $threadlist[$key] = $thread;
        }

        foreach ($flag_thread as $key => $thread) {
            $threadlist[$key] = $thread;
        }

        // hook model_well_cms_web_index_data_threadlist_end.php

        cache_set('wellcms_index_threadlist', $threadlist);
    }

    // hook model_well_cms_web_index_data_end.php

    return $threadlist;
}

// 首页获取主题属性 返回各属性主题数据
function well_cms_index_flag_thread()
{
    // hook model_well_cms_index_flag_thread_start.php

    $well_conf = $_SERVER['well_conf']['setting']['index'];

    $page = 1;
    $slides = 1;    //轮播
    $headline = 2;  //头条
    $guide = 3;     //导读
    $recommend = 4; //导读

    // hook model_well_cms_index_flag_thread_before.php

    $thread_slides = well_cms_cache_thread_flag($slides, $page, $well_conf['slides']);

    $thread_headline = well_cms_cache_thread_flag($headline, $page, $well_conf['headline']);

    $thread_guide = well_cms_cache_thread_flag($guide, $page, $well_conf['guide']);

    $thread_recommend = well_cms_cache_thread_flag($recommend, $page, $well_conf['recommend']);

    // hook model_well_cms_index_flag_thread_after.php

    $arr = array(
        'slide' => $thread_slides,
        'headline' => $thread_headline,
        'guide' => $thread_guide,
        'recommend' => $thread_recommend
    );

    // hook model_well_cms_index_flag_thread_end.php

    return $arr;
}

// 获取需要在首页显示的版块主题数据
function well_cms_index_forum_thread($forumlist)
{
    // hook model_well_cms_index_forum_thread_start.php
    if (empty($forumlist)) return array();

    $orderby = array('tid' => -1);
    $page = 1;

    $arr = $threadarr = array();

    // hook model_well_cms_index_forum_thread_before.php

    // 遍历所有在首页显示内容的版块
    $fidarr = well_cms_index_forum($forumlist);

    $headline_tids = $new_tids = array();

    // hook model_well_cms_index_forum_thread_after.php

    foreach ($fidarr as $_forum) {

        // hook model_well_cms_index_forum_thread_fidarr_start.php
        $arr[$_forum['fid']] = $_forum;

        // 栏目头条内容tidarr
        $headlines = well_cms_thread_flag_tid_find(array('flag' => 2, 'fid' => $_forum['fid']), array('tid' => -1), 1, $_forum['well_headlines']);

        // hook model_well_cms_index_forum_thread_fidarr_before.php

        // 头条按栏目分组
        foreach ($headlines as $v) {
            $headline_tids[] = $v;
        }

        // hook model_well_cms_index_forum_thread_fidarr_after.php

        $forum_thread = db_find('thread', array('fid' => $_forum['fid']), $orderby, $page, $_forum['well_news'], 'tid', array('tid'));
        // 最新信息按栏目分组
        foreach ($forum_thread as $_thread) {
            $new_tids[] = $_thread['tid'];
        }

        // hook model_well_cms_index_forum_thread_fidarr_end.php
    }

    // 合并tid
    $tidarr = array_merge($new_tids, $headline_tids);

    // hook model_well_cms_index_forum_thread_merge.php

    $tidarr = array_unique($tidarr);

    $pagesize = count($tidarr);

    // hook model_well_cms_index_forum_thread_count.php

    // 遍历获取的所有tid主题
    $threadlist = well_cms_thread_find($tidarr, $orderby, $page, $pagesize);

    //well_cms_thread_decode_format($threadlist);

    foreach ($threadlist as &$_thread) {
        // 各版块头条内容
        if (in_array($_thread['tid'], $headline_tids)) {
            $arr[$_thread['fid']]['headline'][$_thread['tid']] = $_thread;
        }
        // 各版块最新内容
        if (in_array($_thread['tid'], $new_tids)) {
            $arr[$_thread['fid']]['news'][$_thread['tid']] = $_thread;
        }
        // hook model_well_cms_index_forum_thread_foreach_threadlist.php
    }

    $arr = arrlist_multisort($arr, 'rank', TRUE);
    //krsort($arr);
    $threadarr['thread'] = $arr;

    return $threadarr;
}

// 获取需要在首页显示的版块
// fid name well_news最新显示数量 well_headlines头条显示数量
function well_cms_index_forum($forumlist)
{
    if (empty($forumlist)) return array();

    // hook model_well_cms_index_forum_start.php

    $arr = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_display' => 1), array(), 1, 1000);

    $forum = array();
    foreach ($arr as $key => $v) {
        $forum[$v['fid']] = array(
            'fid' => $v['fid'],
            'name' => $v['name'],
            'rank' => $v['rank'],
            'well_type' => $v['well_type'],
            'url' => well_nav_url_format($v),
            'well_news' => $v['well_news'],
            'well_headlines' => $v['well_headlines'],
            // hook model_well_cms_index_forum_foreach.php
        );
    }

    // hook model_well_cms_index_forum_end.php

    return $forum;
}

//--------------频道----------------
// 频道数据
function well_cms_web_channel_data($well_fup, $forumlist)
{
    // hook model_well_cms_web_channel_data_start.php

    if (empty($forumlist[$well_fup])) return array();

    $r = well_check_forumlist_channel($well_fup, $forumlist);
    if ($r === FALSE) return array();

    $cache_key = 'wellcms_channel' . $well_fup . '_threadlist';
    $threadlist = cache_get($cache_key);

    if ($threadlist === NULL) {

        $flag_thread = well_cms_channel_flag_thread($well_fup, $forumlist);

        $forum_thread = well_cms_channel_forum_thread($well_fup, $forumlist);

        // hook model_well_cms_web_channel_data_before.php

        $threadlist = array();

        foreach ($forum_thread as $key => $thread) {
            $threadlist[$key] = $thread;
        }

        foreach ($flag_thread as $key => $thread) {
            $threadlist[$key] = $thread;
        }

        // hook model_well_cms_web_channel_data_threadlist.php

        cache_set($cache_key, $threadlist, 60);
    }

    // hook model_well_cms_web_channel_data_end.php

    return $threadlist;
}

// 频道获取主题属性 返回各属性主题数据
function well_cms_channel_flag_thread($well_fup, $forumlist)
{
    if (empty($forumlist[$well_fup])) return array();

    // hook model_well_cms_channel_flag_thread_start.php

    $forum = $forumlist[$well_fup];

    $page = 1;
    $slides = 1;    //轮播
    $headline = 2;  //头条
    $guide = 3;     //导读
    $recommend = 4; //导读

    // hook model_well_cms_channel_flag_thread_before.php

    $thread_slides = well_cms_cache_channel_thread_flag($slides, $well_fup, $page, $forum['well_channel_slides']);

    $thread_headline = well_cms_cache_channel_thread_flag($headline, $well_fup, $page, $forum['well_channel_headline']);

    $thread_guide = well_cms_cache_channel_thread_flag($guide, $well_fup, $page, $forum['well_channel_guide']);

    $thread_recommend = well_cms_cache_channel_thread_flag($recommend, $well_fup, $page, $forum['well_channel_recommend']);

    // hook model_well_cms_channel_flag_thread_after.php

    $arr = array(
        'slide' => $thread_slides,
        'headline' => $thread_headline,
        'guide' => $thread_guide,
        'recommend' => $thread_recommend
    );

    // hook model_well_cms_channel_flag_thread_end.php

    return $arr;
}

// 获取需要在频道显示的版块主题数据
function well_cms_channel_forum_thread($well_fup, $forumlist)
{
    // hook model_well_cms_channel_forum_thread_start.php

    if (empty($forumlist[$well_fup])) return array();

    $orderby = array('tid' => -1);
    $page = 1;

    $arr = $threadarr = array();

    // hook model_well_cms_channel_forum_thread_before.php

    // 获取需要在首页显示的版块
    $fidarr = well_cms_channel_forum($well_fup, $forumlist);

    $headline_tids = $new_tids = array();

    // hook model_well_cms_channel_forum_thread_after.php

    foreach ($fidarr as $_forum) {

        // hook model_well_cms_channel_forum_thread_fidarr_start.php
        $arr[$_forum['fid']] = $_forum;

        // 各板块的2条头条内容
        $headlines = well_cms_thread_flag_tid_find(array('flag' => 2, 'fid' => $_forum['fid']), array('tid' => -1), 1, $_forum['well_channel_headlines']);

        // hook model_well_cms_channel_forum_thread_fidarr_before.php

        // 头条按栏目分组
        foreach ($headlines as $v) {
            $headline_tids[] = $v;
        }

        // hook model_well_cms_channel_forum_thread_fidarr_after.php

        $forum_thread = db_find('thread', array('fid' => $_forum['fid']), $orderby, $page, $_forum['well_channel_news'], 'tid', array('tid'));
        // 最新信息按栏目分组
        foreach ($forum_thread as $_thread) {
            $new_tids[] = $_thread['tid'];
        }

        // hook model_well_cms_channel_forum_thread_fidarr_end.php
    }

    // 合并tid
    $tidarr = array_merge($new_tids, $headline_tids);

    // hook model_well_cms_channel_forum_thread_merge.php

    $tidarr = array_unique($tidarr);
    $pagesize = count($tidarr);

    // 遍历获取的所有tid主题
    $threadlist = well_cms_thread_find($tidarr, $orderby, $page, $pagesize);

    foreach ($threadlist as $_thread) {
        // 各版块头条内容
        if (in_array($_thread['tid'], $headline_tids)) {
            $arr[$_thread['fid']]['headline'][$_thread['tid']] = $_thread;
        }
        // 各版块最新内容
        if (in_array($_thread['tid'], $new_tids)) {
            $arr[$_thread['fid']]['news'][$_thread['tid']] = $_thread;
        }
        // hook model_well_cms_channel_forum_thread_foreach_threadlist.php
    }
    $arr = arrlist_multisort($arr, 'rank', TRUE);
    //krsort($arr);
    $threadarr['thread'] = $arr;

    return $threadarr;
}

// 获取需要在频道显示的版块
// fid name well_channel_news最新显示数量 well_channel_headlines头条显示数量
function well_cms_channel_forum($well_fup, $forumlist)
{
    if (empty($forumlist[$well_fup])) return array();

    // hook model_well_cms_channel_forum_start.php

    $arr = arrlist_cond_orderby($forumlist, array('well_fup' => $well_fup, 'well_type' => 1, 'well_channel_display' => 1), array(), 1, 1000);

    $forum = array();
    foreach ($arr as $key => $v) {
        $forum[$v['fid']] = array(
            'fid' => $v['fid'],
            'name' => $v['name'],
            'rank' => $v['rank'],
            'url' => well_nav_url_format($v),
            'well_channel_news' => $v['well_channel_news'],
            'well_channel_headlines' => $v['well_channel_headlines'],
            // hook model_well_cms_channel_forum_foreach.php
        );
    }

    // hook model_well_cms_channel_forum_end.php

    return $forum;
}

//--------------cache----------------
// 内容页右侧列表
function wel_cms_thread_info_list($fid = 0, $list_headlines = 0, $list_recommends = 0, $list_news = 0)
{
    if (!$fid) return array();

    // hook model_wel_cms_thread_info_list_start.php
    // 头条
    $headlinelist = well_cms_cache_forum_thread_flag(2, $fid, 1, $list_headlines);
    // 推荐
    $recommendlist = well_cms_cache_forum_thread_flag(4, $fid, 1, $list_recommends);

    $arr = array('headline' => $headlinelist, 'recommend' => $recommendlist);

    // hook model_wel_cms_thread_info_list_end.php

    return $arr;
}

//--------------其他----------------

// 获取热门标签
function well_cms_web_hot_tag()
{
    // hook model_well_cms_web_hot_tag_start.php
    $taglist = cache_get('wellcms_hot_tag');
    if ($taglist === NULL) {
        $taglist = well_cache_hot_tag_find();
        cache_set('wellcms_hot_tag', $taglist);
    }
    // hook model_well_cms_web_hot_tag_end.php
    return $taglist;
}

// 获取需要显示的导航数据 fid name
function well_cms_web_nav($forumlist)
{
    if (empty($forumlist)) return array();

    // hook model_well_cms_web_nav_start.php

    $arr = arrlist_cond_orderby($forumlist, array('well_nav_display' => 1), array(), 1, 1000);
    $arr = arrlist_multisort($arr, 'rank', TRUE);
    $nav = array();
    foreach ($arr as $key => $v) {
        $nav[$v['fid']] = array(
            'fid' => $v['fid'],
            'name' => $v['name'],
            'well_forum_type' => $v['well_forum_type'],
            'url' => well_nav_url_format($v),
            // hook model_well_cms_web_nav_foreach.php
        );
    }

    // hook model_well_cms_web_nav_end.php

    return $nav;
}

// 导航 格式化URL
function well_nav_url_format($forum)
{
    // hook model_well_nav_url_format_start.php
    // CMS
    if ($forum['well_type']) {
        if ($forum['well_forum_type'] == 1) {
            // 频道
            $url = url('channel-' . $forum['fid']);
            // hook model_well_nav_url_format_channel.php
        } else {
            // 列表
            $url = url('list-' . $forum['fid']);
            // hook model_well_nav_url_format_list.php
        }
    } else {
        $url = url('forum-' . $forum['fid']);
        // hook model_well_nav_url_format_forum.php
    }
    // hook model_well_nav_url_format_end.php
    return $url;
}

// 返回cms版块列表 array('fid' => 'name')
function well_cms_forumarr($forumlist)
{
    $menulist = well_get_cms_forumlist($forumlist);
    $forumarr = arrlist_key_values($menulist, 'fid', 'name');

    return $forumarr;
}

// 返回bbs版块 array('fid' => 'name')
function well_bbs_forumarr($forumlist)
{
    global $gid;

    $bbslist = well_get_bbs_list($forumlist);
    $bbslist = forum_list_access_filter($bbslist, $gid);
    $forumarr = arrlist_key_values($bbslist, 'fid', 'name');

    return $forumarr;
}

// 加载模板
function well_cms_template_htm($type, $forum = array())
{
    $conf = $_SERVER['conf'];
    $well_conf = $_SERVER['well_conf']['setting'];
    // hook model_well_cms_template_htm_start.php

    !isset($conf['mobile']) AND $conf['mobile'] = 0;

    // 用户自行开发模板路径 此处非hook形式，需上传模板到以下目录
    $template_path = APP_PATH . './plugin/well_cms/view/template/';

    // 可hook模板路径
    // hook model_well_cms_template_htm_before.php

    // 0电脑 1微信 2手机浏览器 3pad
    $detect = well_detect_device();

    $template_page = $pre = '';
    if ($conf['mobile'] && $detect) {
        if ($conf['mobile'] == 2 && $detect == 3) {
            $pre = 'pad.';
        } else {
            $pre = 'm.';
        }
    }

    // hook model_well_cms_template_htm_after.php

    // 默认模板路径
    $default_template_path = APP_PATH . './plugin/well_cms/view/htm/';

    if ($type == 1) {

        // hook model_well_cms_template_web_type_htm_start.php

        if ($well_conf['web_type'] == 1) {
            // 扁平首页
            $index = $template_path . $pre . 'index.htm';
            $template_page = (file_exists($index)) ? $index : $default_template_path . 'index.flat.htm';
        } else {
            // 门户首页
            $index = $template_path . $pre . 'index.htm';
            $template_page = (file_exists($index)) ? $index : $default_template_path . 'index.htm';
        }

        // hook model_well_cms_template_web_type_htm_end.php

    } elseif ($type == 2) {

        // 频道&列表 well_forum_type
        $well_cate_tpl = $template_path . $pre . $forum['well_tpl_class'];

        $template_page = (!empty($forum['well_tpl_class']) AND file_exists($well_cate_tpl)) ? $well_cate_tpl : ($forum['well_forum_type'] ? $default_template_path . 'channel.htm' : $default_template_path . 'list.htm');

    } elseif ($type == 3) {

        // 内容页
        $well_show_tpl = $template_path . $pre . $forum['well_tpl_show'];

        $template_page = (!empty($forum['well_tpl_show']) AND file_exists($well_show_tpl)) ? $well_show_tpl : $default_template_path . 'thread.htm';

    }

    // hook model_well_cms_template_htm_end.php

    return $template_page;
}

function well_get_forumarr($forumlist)
{
    $res = _COOKIE('well_forumarr');
    if ($res == 'CMS') {
        // CMS版块
        $forumarr = well_cms_forumarr($forumlist);
    } else {
        // BBS版块
        $forumarr = well_bbs_forumarr($forumlist);
    }
    return $forumarr;
}

function well_set_cookie_forumarr($value)
{
    well_cookie_set('well_forumarr', $value, 86400);
}

// 清理缓存
function well_cms_delete_cache()
{
    // hook model_well_cms_delete_cache_start.php
    cache_delete('wellcms_index_threadlist');
    cache_delete('wellcms_hot_tag');
    // hook model_well_cms_delete_cache_end.php
}

// hook model_well_web_end.php

?>