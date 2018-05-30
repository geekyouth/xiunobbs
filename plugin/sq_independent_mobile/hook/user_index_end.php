if(IS_MOBILE) {
	// include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/user.htm');
	// return;

	http_location(url('user-thread-' . $_uid));
	return;
}