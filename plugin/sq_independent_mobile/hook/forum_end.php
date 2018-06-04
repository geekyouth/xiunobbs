<?php exit;
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