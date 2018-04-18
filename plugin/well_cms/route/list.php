<?php

!defined('DEBUG') AND exit('Access Denied.');

// hook well_list_start.php
$fid = param(1, 0);
$page = param(2, 1);
$orderby = param('orderby');
$extra = array(); // 给插件预留

$active = 'default';
!in_array($orderby, array('tid', 'lastpid')) AND $orderby = 'lastpid';
$extra['orderby'] = $orderby;

$forum = forum_read($fid);
empty($forum) AND message(3, lang('forum_not_exists'));
forum_access_user($fid, $gid, 'allowread') OR message(-1, lang('insufficient_visit_forum_privilege'));
$pagesize = $forum['well_pagesize'];

// hook well_list_top_list_before.php

$toplist = $page == 1 ? thread_top_find($fid) : array();

// 从默认的地方读取主题列表
$thread_list_from_default = 1;

// hook well_list_thread_list_before.php

if($thread_list_from_default) {
	$pagination = pagination(url("list-$fid-{page}", $extra), $forum['threads'], $page, $pagesize);
	$threadlist = thread_find_by_fid($fid, $page, $pagesize, $orderby);
}

// hook well_list_threadlist_before.php

// 处理wellcms主题附表
if ($forum['well_type'] && $threadlist) {
    $well_tidarr = arrlist_values($threadlist, 'tid');
    well_cms_well_thread_format($threadlist, $pagesize, $well_tidarr);

    // hook well_thread_format_before.php

    $well_thread_list = wel_cms_thread_info_list($fid, $forum['well_list_headlines'], $forum['well_list_recommends'], $forum['well_list_news']);
    // 头条
    $headlinelist = array_value($well_thread_list, 'headline');
    // 推荐
    $recommendlist = array_value($well_thread_list, 'recommend');

    // hook well_list_recommend_before.php
}

// hook well_list_well_thread_before.php

$header['title'] = $forum['seo_title'] ? $forum['seo_title'] : $forum['name'].'-'.$conf['sitename'];
$header['mobile_title'] = $forum['name'];
$header['mobile_link'] = well_nav_url_format($forum);
$header['keywords'] = '';
$header['description'] = $forum['brief'];
well_set_cookie_forumarr('CMS');
$_SESSION['fid'] = $fid;

// hook well_list_end.php

if ($ajax) {
    $forum = forum_safe_info($forum);
    foreach ($threadlist as &$thread) $thread = thread_safe_info($thread);
    message(0, array('list' => $forum, 'threadlist' => $threadlist));
} else {
    // hook well_list_template_htm.php
    include _include(well_cms_template_htm(2, $forum));
}
?>