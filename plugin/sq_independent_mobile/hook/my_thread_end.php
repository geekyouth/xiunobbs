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

	$dontShowAuthor = true;

	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/my_thread.htm');
	return;
}