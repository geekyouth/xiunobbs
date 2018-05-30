if(IS_MOBILE) {
	// $show_search = 2;

	$tagid = param(3, 0);
	if($tagid) {
		$tag = db_find_one('tag', ['tagid' => $tagid]);
	} else {
		$tag = ['name' => '无分类'];
	}

	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/post.htm');
	return;
}