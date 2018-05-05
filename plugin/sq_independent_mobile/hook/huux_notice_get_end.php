if(IS_MOBILE) {
	$notice_menu = include _include(APP_PATH . SQ_MOBILE_PATH . '/conf/notice_menu.conf.php');

	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/my_notice.htm');
	return;
}