if(IS_MOBILE) {
	$is_forum = true;
	include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/forum.htm');
	exit();
}