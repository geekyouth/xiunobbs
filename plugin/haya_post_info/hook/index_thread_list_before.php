<?php
exit;

if (isset($haya_post_info_config['show_setting_forum']) 
	&& $haya_post_info_config['show_setting_forum'] == 1
) {
	$thread_list_from_default = 0;

	$haya_post_info_show_fids = explode(',', $haya_post_info_config['index_show_fids']);
	$haya_post_info_threads = haya_post_info_thread_count(array('fid' => $haya_post_info_show_fids));
	$threadlist = haya_post_info_thread_find_by_fids($haya_post_info_show_fids, $haya_post_info_threads, $page, $pagesize, $order);
	$pagination = pagination(url("index-{page}"), $haya_post_info_threads, $page, $pagesize);
}

?>