<?php

/*
	Xiuno BBS 4.0 用户组升级增强版
	插件由查鸽信息网制作网址：http://cha.sgahz.net/
*/

!defined('DEBUG') AND exit('Forbidden');
$kv = kv_get('sg_group');
if($kv) {
	$sg_group= array('up_group'=>$kv['group1'], 'create_credits'=>$kv['group2'], 'post_credits'=>$kv['group3'], 'isfirst'=>$kv['group4'], 'creditsfrom'=>$kv['group5'], 'creditsto'=>$kv['group6']);
	setting_set('sg_group', $sg_group);
	kv_delete('sg_group');
}

?>