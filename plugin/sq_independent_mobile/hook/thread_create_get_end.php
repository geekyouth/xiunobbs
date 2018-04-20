if(IS_MOBILE) {
	$is_thread_create = true;
	include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/post.htm');
	exit();
}