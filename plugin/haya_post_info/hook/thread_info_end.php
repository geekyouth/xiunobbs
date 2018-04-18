<?php
exit;

$haya_post_info_param = array();

if (isset($haya_post_info_config['show_post_sort']) 
	&& $haya_post_info_config['show_post_sort'] == 1
) {
	$haya_post_info_post_default_sort = isset($haya_post_info_config['post_default_sort']) ? trim($haya_post_info_config['post_default_sort']) : '';
	$haya_post_info_orderby = param('sort', $haya_post_info_post_default_sort);
	if (!empty($haya_post_info_orderby)) {
		$haya_post_info_param = array_merge($haya_post_info_param, array('sort' => trim($haya_post_info_orderby)));
	}
}

if ((isset($haya_post_info_config['show_see_him']) 
	&& $haya_post_info_config['show_see_him'] == 1)
	|| (isset($haya_post_info_config['show_see_first_floor']) 
	&& $haya_post_info_config['show_see_first_floor'] == 1)
) {
	$haya_post_info_see_user = param('user', '');
	if (!empty($haya_post_info_see_user)) {
		$haya_post_info_see_user_id = intval($haya_post_info_see_user);

		$thread['posts'] = post_count(array(
			'tid' => $thread['tid'], 
			'isfirst' => 0,
			'uid' => $haya_post_info_see_user_id, 
		));
		
		$haya_post_info_param = array_merge($haya_post_info_param, array('user' => $haya_post_info_see_user_id));
	}
}

if (!empty($haya_post_info_param)) {
	$pagination = pagination(url("thread-$tid-{page}$keywordurl", $haya_post_info_param), $thread['posts'] + 1, $page, $pagesize);
}

?>
