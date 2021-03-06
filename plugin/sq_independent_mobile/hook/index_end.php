<?php exit;
// 处理置顶，重新排序数组
// sort($threadlist);

$is_index = 1; // 主页
$is_index_one = ($page == 1); // 判断是否主页并且是第一页
// $showImageMaxSize = '300000'; // 最大图片显示k

if($order == $conf['order_default']) {
	$toplist3 = thread_top_find(0);
}

include _include(APP_PATH . SQ_MOBILE_PATH . '/model/plugin.func.php');

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

if(IS_MOBILE) {
	foreach($toplist3 as $key => &$value) {
		unset($threadlist[$key]); // 将贴列表里面置顶的贴删除，以免重复，那么这个threadlist就是新贴
		if($value['images']) { // 将预览图片添加到置顶区的列表项里面
			$imageList = get_images_by_tid($value['tid'], true); // 获得缩略图
			$value['images_url'] = $imageList ? $imageList : [] ;
		} else { // 如果没缩略图，看看有没有简介
			$desc = get_desc_by_tid($value['tid']);
			if($desc) {
				$value['sq_desc'] = $desc;
			}
		}
	}

	$show_search = 1; // 用来显示搜索框
	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/index.htm');
	return;
} else {
	$hotList = get_hot_thread(3); // 热门贴
	
	if($digest == 1) $is_index = 0; // 如果是精华帖页，不显示那3个框

	foreach($toplist3 as $key => $value) {
		unset($threadlist[$key]); // 将贴列表里面置顶的贴删除，以免重复，那么这个threadlist就是新贴
	}
	
	$lastThread = thread_find_by_fids($fids, 1, 3, 'tid', $threads); // 查找最新的3张贴
	if ($page == 1) { // 只有在第一页的时候才去掉重复的
		foreach($lastThread as $k => $v) {
			unset($threadlist[$k]); // 去重
		}
	}
	
	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/pc_htm/index.htm'); // PC端首页
	return;
}