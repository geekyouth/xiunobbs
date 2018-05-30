if(IS_MOBILE) {
	// $show_search = 2; // 头部不显示东西

	foreach($postlist as &$_post) { // 添加图片到postlist里面
		$imgs = get_imgs_by_postid($_post['pid']);
		if($imgs) {
			$_post['sq_imgs'] = $imgs;
		}
	}

	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/thread.htm');
	return;
}