if(IS_MOBILE) {
	// 处理一个$filelistSq 的问题
	list($attachlist, $imagelist, $filelist) = attach_find_by_pid($pid); // 直接让其赋值
	$filelistSq = $imagelist;
	if(!$post['flies']) {
		$attachlist = $filelist = [];
	}

	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/post.htm');
	return;
}