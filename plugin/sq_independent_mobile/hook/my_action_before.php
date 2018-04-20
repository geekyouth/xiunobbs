if(empty($action)) {
	$is_my = true;
	$header['title'] = lang('my_home');
	include _include(APP_PATH . SQ_MOBILE_PATH . 'view/htm/my.htm');
	exit();
}