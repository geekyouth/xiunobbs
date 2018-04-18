<?php
!defined('DEBUG') AND exit('Access Denied.');

// hook well_index_start.php

$header['title'] = $conf['sitename'];
$header['keywords'] = '';
$header['description'] = $conf['sitebrief'];
$_SESSION['fid'] = 0;
well_set_cookie_forumarr('CMS');
$well_index = array();
$well_tag = array();

// hook well_index_before.php

$well_conf = $_SERVER['well_conf']['setting'];
if ($well_conf['web_type'] == 0) {

    // hook well_index_web_type_start.php
    $well_index = well_cms_web_index_data($forumlist);

    // hook well_index_web_type_before.php

    $slide = array_value($well_index, 'slide');
    $headline = array_value($well_index, 'headline');
    $guide = array_value($well_index, 'guide');
    $recommend = array_value($well_index, 'recommend');
    $forums = array_value($well_index, 'thread');

    // hook well_index_web_type_after.php

    // 轮播凑整 双列排版 防止错版 单一列注释该代码
    if ($slide) {
        if (count($slide) % 2 != 0) {
            $i = 0;
            foreach ($slide as $key => &$_thread) {
                $i++;
                if ($i == 1) {
                    $slide[] = $_thread;
                }
            }
        }
    }

    $well_tag = well_cms_web_hot_tag();

    // hook well_index_web_type_end.php

} elseif ($well_conf['web_type'] == 1) {

    $page = param(1, 1);
    $order = $conf['order_default'];
    $order != 'tid' AND $order = 'lastpid';
    $pagesize = 15;
    $active = 'default';

    // 从默认的地方读取主题列表
    $thread_list_from_default = 1;

    // hook well_index_thread_list_before.php
    if ($thread_list_from_default) {

        // 过滤BBS版块
        foreach ($forumlist_show as $key => $_forum) {
            if ($_forum['well_type'] == 0) unset($forumlist_show[$key]);
        }

        $fids = arrlist_values($forumlist_show, 'fid');
        $threads = arrlist_sum($forumlist_show, 'threads');
        $pagination = pagination(url("index-{page}"), $threads, $page, $pagesize);

        // hook well_thread_find_by_fids_before.php
        $threadlist = thread_find_by_fids($fids, $page, $pagesize, $order, $threads);
    }

    // 查找置顶帖
    if ($order == $conf['order_default'] && $page == 1) {
        $toplist3 = thread_top_find(0);
        $threadlist = $toplist3 + $threadlist;
    }

    if ($threadlist) {
        $well_tidarr = arrlist_values($threadlist, 'tid');
        well_cms_well_thread_format($threadlist, $pagesize, $well_tidarr);
    }

    // 过滤没有权限访问的主题 / filter no permission thread
    thread_list_access_filter($threadlist, $gid);

    // hook well_index_cache_before.php

    $headline = well_cms_cache_thread_flag(2, 1, 10);

    $recommend = well_cms_cache_thread_flag(4, 1, 10);

    // hook well_index_cache_after.php

    $well_tag = well_cms_web_hot_tag();
}

// hook well_index_end.php

if ($ajax) {
    message(0, array('nav' => $well_nav, 'forum' => $well_index, 'tag' => $well_tag));
} else {
    // hook well_index_template_htm.php
    include _include(well_cms_template_htm(1));
}
?>