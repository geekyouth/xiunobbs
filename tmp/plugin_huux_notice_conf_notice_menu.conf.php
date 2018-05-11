<?php
// 0:全部 1:公告(1.6版本已取消) 2:评论 3:系统(主题通知，关注通知等)  99:其他
// 接入的插件请务必按照规范的格式写
$notice_menu = array(
	0 => array(
		'url'=>url('my-notice'), 
		'name'=>lang('notice_lang_all'), 
		'class'=>'info',
		'icon'=>''
	),
	2 => array(
		'url'=>url('my-notice-2'), 
		'name'=>lang('notice_lang_comment'),
		'class'=>'primary',
		'icon'=>''
	),
	3 => array(
		'url'=>url('my-notice-3'), 
		'name'=>lang('notice_lang_system'),
		'class'=>'danger',
		'icon'=>''
	),
	

155 => array(
	'url' => url('my-notice-155'), 
	'name' => lang('haya_favorite_name'),
	'class' => 'success',
	'icon' => ''
),



156 => array(
	'url' => url('my-notice-156'), 
	'name' => '@我',
	'class' => 'info',
	'icon' => ''
),



150 => array(
	'url' => url('my-notice-150'), 
	'name' => lang('haya_post_like_notice'),
	'class' => 'success',
	'icon' => ''
),


	99 => array(
		'url'=>url('my-notice-99'), 
		'name'=>lang('notice_lang_other'),
		'class'=>'success',
		'icon'=>'bell'
	)
);


if (isset($haya_post_info_config['at_user_to_notice']) 
	&& $haya_post_info_config['at_user_to_notice'] != 1
) {
	unset($notice_menu[156]);
}


return $notice_menu;
?>