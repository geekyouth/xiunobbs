<?php
/*
	Xiuno BBS 4.0 用户组升级增强版
	插件由查鸽信息网制作网址：http://cha.sgahz.net/
*/

!defined('DEBUG') AND exit('Forbidden');
$r = kv_delete('sg_group');
$r = setting_delete('sg_group');
$r === FALSE AND message(-1, '卸载失败');

?>