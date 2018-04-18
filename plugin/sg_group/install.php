<?php

/*
	Xiuno BBS 4.0 用户组升级增强版
	插件由查鸽信息网制作网址：http://cha.sgahz.net/
*/
!defined('DEBUG') AND exit('Forbidden');
// 初始化
$kv = setting_get('sg_group');
if(!$kv) {
	$kv = array('up_group'=>'3', 'create_credits'=>'2', 'post_credits'=>'1', 'isfirst'=>'1', 'creditsfrom'=>'2', 'creditsto'=>'10');
	setting_set('sg_group', $kv);
}
?>