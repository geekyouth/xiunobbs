<?php

// 分页
function haya_post_info_pagination_tpl($url, $text, $active = '') {
	$haya_post_info_pagination_tpl = '<a href="{url}" class="px-1 haya-post-info-link {active}">{text}</a>';
	return str_replace(array('{url}', '{text}', '{active}'), array($url, $text, $active), $haya_post_info_pagination_tpl);
}

function haya_post_info_pagination($url, $totalnum, $page, $pagesize = 20) {
	$totalpage = ceil($totalnum / $pagesize);
	if ($totalpage < 2) return '';
	$page = min($totalpage, $page);
	$shownum = 2;

	$start = 1;
	$end = min($totalpage, $page + $shownum);

	$right = $page + $shownum - $totalpage;
	$right > 0 && $start = max(1, $start -= $right);
	$left = $page - $shownum;
	$left < 0 && $end = min($totalpage, $end -= $left);

	$s = '';
	if ($start > 1) {
		$s .= haya_post_info_pagination_tpl(str_replace('{page}', 1, $url), '1'.($start > 2 ? '...' : ''));
	}
	for ($i = $start; $i <= $end; $i++) {
		$s .= haya_post_info_pagination_tpl(str_replace('{page}', $i, $url), $i, $i == $page ? ' active' : '');
	}
	if ($end != $totalpage) {
		$s .= haya_post_info_pagination_tpl(str_replace('{page}', $totalpage, $url), ($totalpage - $end > 1 ? '...' : '').$totalpage);
	}
	return $s;
}

// 重写首页数据
function haya_post_info_thread_count($cond = array()) {
	$threads = thread_count($cond);
	return $threads;
}

function haya_post_info_thread__find_by_fids($fids, $threads = 100, $page = 1, $pagesize = 20, $order = 'lastpid') {
	global $conf, $runtime;
	
	$cond = array();
	if (!empty($fids)) {
		$cond['fid'] = $fids;
	}
	
	$desc = TRUE;
	$limitpage = 50000; // 如果需要防止 CC 攻击，可以调整为 5000
	if ($page > 100) {
		$totalpage = ceil($threads / $pagesize);
		$halfpage = ceil($totalpage / 2);
		if ($halfpage > $limitpage && $page < ($totalpage - $limitpage)) {
			$page = $limitpage;
		}
		if ($page > $halfpage) {
			$page = max(1, $totalpage - $page + 1) ;
			$threadlist = thread_find($cond, array($order=>1), $page, $pagesize);
			$threadlist = array_reverse($threadlist, TRUE);
			$desc = FALSE;
		}
	}
	if ($desc) {
		$orderby = array($order=>-1);
		$threadlist = thread_find($cond, $orderby, $page, $pagesize);
	}
	
	return $threadlist;
}

function haya_post_info_thread_find_by_fids($fids, $threads = 100, $page = 1, $pagesize = 20, $order = 'lastpid') {
	global $conf;

	$threadlist = haya_post_info_thread__find_by_fids($fids, $threads, $page, $pagesize, $order);
	
	if ($order == $conf['order_default'] && $page == 1) {
		$toplist3 = thread_top_find(0);
		// $toplist1 = thread_top_find($fids); 取消板块首页置顶
		$threadlist = $toplist3 + $threadlist;
	}
	
	return $threadlist;
}

?>
