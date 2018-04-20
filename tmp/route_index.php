<?php

/*
* Copyright (C) 2015 xiuno.com
*/

!defined('DEBUG') AND exit('Access Denied.');



$page = param(1, 1);
$order = $conf['order_default'];
$order != 'tid' AND $order = 'lastpid';
$pagesize = $conf['pagesize'];
$active = 'default';

// 从默认的地方读取主题列表
$thread_list_from_default = 1;



if (isset($haya_post_info_config['show_setting_forum']) 
	&& $haya_post_info_config['show_setting_forum'] == 1
) {
	$thread_list_from_default = 0;

	$haya_post_info_show_fids = explode(',', $haya_post_info_config['index_show_fids']);
	$haya_post_info_threads = haya_post_info_thread_count(array('fid' => $haya_post_info_show_fids));
	$threadlist = haya_post_info_thread_find_by_fids($haya_post_info_show_fids, $haya_post_info_threads, $page, $pagesize, $order);
	$pagination = pagination(url("index-{page}"), $haya_post_info_threads, $page, $pagesize);
}

$digest = param(2, 0);
if($digest == 1) {
	$thread_list_from_default = 0;
	$active = 'digest';
	$digests = thread_digest_count($fid);
	
	$pagination = pagination(url("$route-{page}-1"), $digests, $page, $pagesize);
	
	$threadlist = thread_digest_find_by_fid($fid, $page, $pagesize);
}
if($thread_list_from_default) {
	$fids = arrlist_values($forumlist_show, 'fid');
	
	$threads = arrlist_sum($forumlist_show, 'threads');
	$pagination = pagination(url("$route-{page}"), $threads, $page, $pagesize);
	
	
	$threadlist = thread_find_by_fids($fids, $page, $pagesize, $order, $threads);

}

// 查找置顶帖
if($order == $conf['order_default'] && $page == 1) {
	$toplist3 = thread_top_find(0);
	$threadlist = $toplist3 + $threadlist;
}

// 过滤没有权限访问的主题 / filter no permission thread
thread_list_access_filter($threadlist, $gid);

// SEO
$header['title'] = $conf['sitename']; 				// site title
$header['keywords'] = ''; 					// site keyword
$header['description'] = $conf['sitebrief']; 			// site description
$_SESSION['fid'] = 0;



$header['keywords'] = empty($conf['site_keywords']) ? '': $conf['site_keywords'];

// 处理置顶，重新排序数组
// sort($threadlist);

if(IS_MOBILE) {
	$is_index = 1; // 用来显示搜索框
	include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/index.htm');
	exit();
}

include _include(APP_PATH.'view/htm/index.htm');

?>