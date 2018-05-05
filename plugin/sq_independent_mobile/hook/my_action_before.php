if(empty($action) && IS_MOBILE) {
	// $show_search = 2;
	$header['title'] = lang('my_home');
	include _include(APP_PATH . SQ_MOBILE_PATH . 'view/htm/my.htm');
	return;
}