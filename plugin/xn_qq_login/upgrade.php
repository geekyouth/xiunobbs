<?php

/*
	Xiuno BBS 4.0 插件实例：第三方登陆安装
	admin/plugin-install-xn_oauth.htm
*/

!defined('DEBUG') AND exit('Forbidden');

$maxuid = db_maxid('user_open_plat', 'uid');

// 如果存在 
$arr = db_find_one('user_open_plat');
if(!empty($arr)) {
	
	// 新的用户
	$arrlist = db_find('user_oauth', array('uid'=>array('>'=>$maxuid)));
	foreach ($arrlist as $new) {
		$openid = $new['openid'];
		$old = db_find_one('user_open_plat', array('openid'=>$new['openid']));
		// 冲突的用户
		if($old) {
			// 所有资源重新关联到老用户
			$newuid = $new['uid'];
			$olduid = $old['uid'];
			db_update('thread', array('uid'=>$newuid), array('uid'=>$olduid, 'lastuid'=>$olduid));
			db_update('post', array('uid'=>$newuid), array('uid'=>$olduid));
			db_update('attach', array('uid'=>$newuid), array('uid'=>$olduid));
			db_update('mythread', array('uid'=>$newuid), array('uid'=>$olduid));
			db_update('mypost', array('uid'=>$newuid), array('uid'=>$olduid));
			db_update('session', array('uid'=>$newuid), array('uid'=>$olduid));
			db_update('modlog', array('uid'=>$newuid), array('uid'=>$olduid));
			db_delete('user_oauth', array('uid'=>$newuid)); // 干掉新用户
		}
	}

	db_exec("REPLACE INTO {$tablepre}user_oauth(uid,openid) SELECT uid,openid FROM {$tablepre}user_open_plat");
	$r = db_exec("UPDATE {$tablepre}user_oauth SET platid='qq'");
	if($r) {
		db_exec("RENAME {$tablepre}user_open_plat TO {$tablepre}user_open_plat_backup");
	}	
}

?>