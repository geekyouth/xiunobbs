<?php



function is_word($s) {
	
	$r = preg_match('#^\\w{1,32}$#', $s, $m);
	return $r;
}

function is_mobile($mobile, &$err) {
	
	if(!preg_match('#^\d{11}$#', $mobile)) {
		$err = lang('mobile_format_mismatch');
		return FALSE;
	}
	
	return TRUE;
}

function is_email($email, &$err) {
	
	$len = mb_strlen($email, 'UTF-8');
	if(strlen($len) > 32) {
		$err = lang('email_too_long', array('length'=>$len));
		return FALSE;
	} elseif(!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/i', $email)) {
		$err = lang('email_format_mismatch');
		return FALSE;
	}
	
	return TRUE;
}

function is_username($username, &$err = '') {
	$ddPluginConf = file_get_contents(APP_PATH . '/plugin/sq_dd_autoLogin/conf.json');
$ddPluginConf = json_decode($ddPluginConf, true);
if ($ddPluginConf['enable'] == 1) { // 如果钉钉自动登陆开启，那么用户名不进行验证
	return true;
}
	$len = mb_strlen($username, 'UTF-8');
	if($len > 16) {
		$err = lang('username_too_long', array('length'=>$len));
		return FALSE;
	} elseif(strpos($username, ' ') !== FALSE || strpos($username, '　') !== FALSE) {
		$err = lang('username_cant_include_cn_space');
		return FALSE;
	} elseif(!preg_match('#^[\w\x{4E00}-\x{9FA5}\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]+$#u', $username)) {
		// 4E00-9FA5(中文)  1100-11FF(朝鲜文) 3130-318F(朝鲜文兼容字母) AC00-D7AF(朝鲜文音节)
		// 4E00-9FA5(chinese)  1100-11FF(korea) 3130-318F(korea compatiable word) AC00-D7AF(korea)
		$err = lang('username_format_mismatch');
		return FALSE;
	}
	

 elseif(qt_check_sensitive_word($username, 'username_sensitive_words', $er)) {
		$err = lang('username_contain_sensitive_word') . $er;
		return FALSE;
	}
	return TRUE;
}

function is_password($password, &$err = '') {
	$len = strlen($password);
	
	if($len == 0) {
		$err = lang('password_is_empty');
		return FALSE;
	} elseif($len != 32) {
		$err = lang('password_length_incorrect');
		return FALSE;
	} elseif($password == 'd41d8cd98f00b204e9800998ecf8427e') {
		$err = lang('password_is_empty');
		return FALSE;
	}
	
	return TRUE;
}


function qt_check_sensitive_word($s, $type, &$error) {
		$sensitive_words = kv_get('qt_sensitive_words');
		if(!isset($sensitive_words[$type])  || !is_array($sensitive_words[$type])) {
			return false;
		}
		foreach($sensitive_words[$type] as $v) {
			if(empty($v)) {
				continue;
			}
			if(strpos(strtolower($s),strtolower($v)) !== false) {
				$error = $v;
				return true;
			}
		}
		return false;
	}

?>