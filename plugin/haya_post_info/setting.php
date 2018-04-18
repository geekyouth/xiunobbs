<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '帖子增强设置';

if ($method == 'GET') {
	
	$config = setting_get('haya_post_info');
	$index_show_fids = explode(',', $config['index_show_fids']);
	
	include _include(APP_PATH . 'plugin/haya_post_info/view/htm/setting.htm');
	
} else {
	
	$config = array();
	
	$config['show_post_sort'] = param('show_post_sort', 0);
	$config['show_see_him'] = param('show_see_him', 0);
	$config['show_see_first_floor'] = param('show_see_first_floor', 0);
	$config['show_list_views'] = param('show_list_views', 0);
	$config['show_index_list_forum'] = param('show_index_list_forum', 0);
	$config['at_user_to_notice'] = param('at_user_to_notice', 0);
	$config['post_default_sort'] = param('post_default_sort', 'asc');
	$config['post_show_first_floor'] = param('post_show_first_floor', 0);
	$config['forum_show_pagination'] = param('forum_show_pagination', 0);
	
	$config['show_setting_forum'] = param('show_setting_forum', 0);
	$fids = param('fid', array());
	$config['index_show_fids'] = implode(',', $fids);
	
	$config['today_thread_hightlighter'] = param('today_thread_hightlighter', 0);
	
	setting_set('haya_post_info', $config); 

	message(0, jump('设置保存成功', url('plugin-setting-haya_post_info')));
	
}

?>
