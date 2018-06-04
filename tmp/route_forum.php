<?php

!defined('DEBUG') AND exit('Access Denied.');


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
$pagesize = $conf['pagesize'];



$toplist = $page == 1 ? thread_top_find($fid) : array();

// 从默认的地方读取主题列表
$thread_list_from_default = 1;



$tagids = param('tagids');
$tagid1 = $tagid2 = $tagid3 = $tagid4 = '';
$threadlist_from_tag = 0;
$find_sql = $count_sql = '';
if($tagids) {
	$thread_list_from_default = 0;
	// 从 tagids 中查找合法的 tagid，不允许任意构造，尽量保证数据关联正确。
	function tag_cate_find_tagid_from_tagids($index, $tagids) {
		global $forum;
		if(empty($forum['tagcatelist'][$index])) return 0;
		$tagcate = $forum['tagcatelist'][$index];
		$enable_tagids = array_keys($tagcate['tagmap']);
		$arr = array_values(array_intersect($enable_tagids, $tagids));
		return empty($arr) ? 0 : $arr[0];
	}
	
	// 结果集缓存下来
	$tagidarr = explode('_', $tagids);
	$tagidarr = array_filter_empty($tagidarr);
	$tagidarr = array_values($tagidarr);
	
	$tagid1 = tag_cate_find_tagid_from_tagids(0, $tagidarr);
	$tagid2 = tag_cate_find_tagid_from_tagids(1, $tagidarr);
	$tagid3 = tag_cate_find_tagid_from_tagids(2, $tagidarr);
	$tagid4 = tag_cate_find_tagid_from_tagids(3, $tagidarr);
	
	$tagidarr = array($tagid1, $tagid2, $tagid3, $tagid4);
	$tagidarr = array_values(array_filter_empty($tagidarr));
	$n = count($tagidarr);
	
	$tidlist = array();
	$tablepre = $db->tablepre;
	$start = ($page - 1) * $pagesize;
	$limit = $pagesize;
	if($n == 1) {
		// 查找 tagid 属于哪个 cateid
		$count_sql = "SELECT COUNT(tid) AS num FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]'";
		$find_sql = "SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' ORDER BY tid DESC LIMIT $start, $limit";
	} elseif($n == 2) {
		$count_sql = "SELECT COUNT(tid) num FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' AND tid IN(
			SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[1]')";
		$find_sql = "SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' AND tid IN(
			SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[1]') ORDER BY tid DESC LIMIT $start, $limit";
	} elseif($n == 3) {
		$count_sql = "SELECT COUNT(tid) num FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' AND tid IN(
			SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[1]' AND tid IN(
				SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[2]'))";
		$find_sql = "SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' AND tid IN(
			SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[1]' AND tid IN(
				SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[2]'))  ORDER BY tid DESC LIMIT $start, $limit";
	} elseif($n == 4) {
		$count_sql = "SELECT COUNT(tid) num FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' AND tid IN(
			SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[1]' AND tid IN(
				SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[2]' AND tid IN(
					SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[3]')))  ORDER BY tid DESC LIMIT $start, $limit";
		$find_sql = "SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[0]' AND tid IN(
			SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[1]' AND tid IN(
				SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[2]' AND tid IN(
					SELECT tid FROM {$tablepre}tag_thread WHERE tagid='$tagidarr[3]')))  ORDER BY tid DESC LIMIT $start, $limit";
	} else {
		$thread_list_from_default = 1;
	}
}

if($thread_list_from_default == 0 && $find_sql) {
	
	// 缓存结果集，不然查询太耗费资源。
	// 针对大站缓存，小站就硬查。
	if($runtime['threads'] > 1000000) {
		$count_sql_md5 = md5($count_sql);
		$find_sql_md5 = md5($find_sql);
		$n = cache_get($count_sql_md5);
		if($n === NULL || DEBUG) {
			$arr = db_sql_find_one($count_sql);
			$n = $arr['num'];
			cache_set($count_sql_md5, $n, 30);
		}
		$tids = cache_get($find_sql_md5);
		if($n === NULL || DEBUG) {
			$tidlist = db_sql_find($find_sql);
			$tids = arrlist_values($tidlist, 'tid');
			cache_set($find_sql_md5, $tids, 30);
		}
	} else {
		$arr = db_sql_find_one($count_sql);
		$n = $arr['num'];
		$tidlist = db_sql_find($find_sql);
		$tids = arrlist_values($tidlist, 'tid');
		unset($arr, $tidlist);
	}
	
	$pagination = pagination(url("forum-$fid-{page}", array('tagids'=>"{$tagid1}_{$tagid2}_{$tagid3}_{$tagid4}")), $n, $page, $pagesize);
	$threadlist = thread_find_by_tids($tids);
	$toplist = array();

}


if($thread_list_from_default) {
	$pagination = pagination(url("forum-$fid-{page}", $extra), $forum['threads'], $page, $pagesize);
	$threadlist = thread_find_by_fid($fid, $page, $pagesize, $orderby);
}

$header['title'] = $forum['seo_title'] ? $forum['seo_title'] : $forum['name'].'-'.$conf['sitename'];
$header['mobile_title'] = $forum['name'];
$header['mobile_link'] = url("forum-$fid");
$header['keywords'] = '';
$header['description'] = $forum['brief'];

$_SESSION['fid'] = $fid;


if(IS_MOBILE) {

	/** 将图片地址添加到列表项里面 */
	foreach($threadlist as &$item) {
		if($item['images']) {
			$imageList = get_images_by_tid($item['tid'], true); // 获得缩略图
			$item['images_url'] = $imageList ? $imageList : []; 
		} else { // 如果没缩略图，看看有没有简介
			$desc = get_desc_by_tid($item['tid']);
			if($desc) {
				$item['sq_desc'] = $desc;
			}
		}
	}

	$tagid  = param('tagids');
	$tagids = explode('_', $tagid);
	$tagid  = $tagids[0]; // 第一个
	if($tagid) {
		$tag = get_tag_by_tagid($tagid);
	} else {
		$tag = ['tagid' => 0, 'name' => '全部'];
	}
	
	$threadsByTag = get_count_by_tagid($tagid);
	$threadCount = count($threadsByTag);
	
	if($threadsByTag) {
		$threadIds = [];
		foreach($threadsByTag as $_item) {
			$threadIds[] = $_item['tid'];
		}
		$threadIds = implode(',', $threadIds);
		$threadIds = rtrim($threadIds, ',');
		$today = count(get_today_by_tids($threadIds));
	} else {
		$today = 0;
	}

	$show_search = 1;
	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/forum.htm');
	return;
}

include _include(APP_PATH.'view/htm/forum.htm');

?>