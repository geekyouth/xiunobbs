<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

if($action == 'digest') {
	$page = param(2, 1);
	$pagesize = 20;
	
	$digests = $user['digests'];
	$pagination = pagination(url("user-$uid-{page}-1"), $digests, $page, $pagesize);
	$threadlist = thread_digest_find_by_uid($uid, $page, $pagesize);
	
	
	
	include _include(APP_PATH.'plugin/xn_digest/view/htm/my_digest.htm');
}
if($action == 'post') {
	
	
	
	$page = param(2, 1);
	$pagesize = 20;
	
	$totalnum = $user['posts'];
	$pagination = pagination(url("my-post-{page}"), $totalnum, $page, $pagesize);
	$postlist = post_find_by_uid($uid, $page, $pagesize);
	
	post_list_access_filter($postlist, $gid);

	
	
	include _include(APP_PATH.'plugin/xn_mypost/view/htm/my_post.htm');
	
}

$user = user_read($uid);
user_login_check();

$header['mobile_title'] = $user['username'];
$header['mobile_linke'] = url("my");

is_numeric($action) AND $action = '';

$active = $action;



if(empty($action)) {
	
	$header['title'] = lang('my_home');
	
	
	
	include _include(APP_PATH.'view/htm/my.htm');
	
/*	
} elseif($action == 'profile') {
	
	if($ajax) {
		// user_safe_info($user);
		message(0, $user);
	} else {
		include _include(APP_PATH.'view/htm/my_profile.htm');
	}
*/
	
} elseif($action == 'password') {
	
	if($method == 'GET') {
		
		
		
		include _include(APP_PATH.'view/htm/my_password.htm');
		
	} elseif($method == 'POST') {
		
		
		
		$password_old = param('password_old');
		$password_new = param('password_new');
		$password_new_repeat = param('password_new_repeat');
		$password_new_repeat != $password_new AND message(-1, lang('repeat_password_incorrect'));
		md5($password_old.$user['salt']) != $user['password'] AND message('password_old', lang('old_password_incorrect'));
		$password_new = md5($password_new.$user['salt']);
		$r = user_update($uid, array('password'=>$password_new));
		$r === FALSE AND message(-1, lang('password_modify_failed'));
		
		
		message(0, lang('password_modify_successfully'));
		
	}
	

} elseif($action == 'thread') {

	
	
	$page = param(2, 1);
	$pagesize = 20;
	$totalnum = $user['threads'];
	
	
	
	$pagination = pagination(url('my-thread-{page}'), $totalnum, $page, $pagesize);
	$threadlist = mythread_find_by_uid($uid, $page, $pagesize);
	
	
	
	include _include(APP_PATH.'view/htm/my_thread.htm');

	
} elseif($action == 'avatar') {
	
	if($method == 'GET') {
		
		
		
		include _include(APP_PATH.'view/htm/my_avatar.htm');
	
	} else {
		
		
		
		$width = param('width');
		$height = param('height');
		$data = param('data', '', FALSE);
		
		empty($data) AND message(-1, lang('data_is_empty'));
		$data = base64_decode_file_data($data);
		$size = strlen($data);
		$size > 2048000 AND message(-1, lang('filesize_too_large', array('maxsize'=>'2M', 'size'=>$size)));
		
		$filename = "$uid.png";
		$dir = substr(sprintf("%09d", $uid), 0, 3).'/';
		$path = $conf['upload_path'].'avatar/'.$dir;
		$url = $conf['upload_url'].'avatar/'.$dir.$filename;
		!is_dir($path) AND (mkdir($path, 0777, TRUE) OR message(-2, lang('directory_create_failed')));
		
		
		file_put_contents($path.$filename, $data) OR message(-1, lang('write_to_file_failed'));
		
		user_update($uid, array('avatar'=>$time));
		
		
		
		message(0, array('url'=>$url));
		
	}
}



elseif ($action == 'favorite') {

	$header['title'] = lang('haya_favorite_my_favorite') . " - " . $conf['sitename'];

	$haya_favorite_config = setting_get('haya_favorite');
	
	
	
	if ($method == 'GET') {
		
		
		$pagesize = intval($haya_favorite_config['user_favorite']);
		$page = param(2, 1);
		$cond['uid'] = $uid; 
		
		$haya_favorite_count = haya_favorite_count($cond);
		$threadlist = haya_favorite_find($cond, array('create_date' => -1), $page, $pagesize);
		$pagination = pagination(url("my-favorite-{page}"), $haya_favorite_count, $page, $pagesize);
		
		
		
		include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite.htm');

	} else {

		$action = param(2, 'add');
		$tid = param('tid');
		if (!$user) {
			message(0, lang('haya_favorite_user_favorite_error_tip'));
		}

		$thread = thread_read($tid);
		empty($thread) AND message(0, lang('thread_not_exists'));
		$haya_check_favorite = haya_favorite_find_by_uid_and_tid($uid, $tid);
		
		$haya_favorite_user_favorite_count = isset($haya_favorite_config['user_favorite_count']) ? intval($haya_favorite_config['user_favorite_count']) : 20;
		
		
		
		if ($action == 'create') {
			
			
			if (!empty($haya_check_favorite)) {
				message(0, lang('haya_favorite_user_have_favorite_tip'));
			}
			
			haya_favorite_create(array(
				'tid' => $tid, 
				'uid' => $user['uid'],
				'create_date' => time(),
				'create_ip' => $longip,
			));
			
			if (isset($haya_favorite_config['favorite_count_type']) 
				&& $haya_favorite_config['favorite_count_type'] == 1
			) {
				$haya_favorite_count = haya_favorite_count(array('tid' => $tid));
				
				thread__update($tid, array('favorites' => $haya_favorite_count));
			} else {
				$haya_favorite_count = $thread['favorites'] + 1;
				
				haya_favorite_thread_user_favorites($tid, 1);
			}
			
			// 更新当前用户收藏数
			$haya_favorite_user_now_favorite_count = haya_favorite_count(array('uid' => $uid));
			user__update($uid, array('favorites' => $haya_favorite_user_now_favorite_count));

			$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
			ob_start();
			include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
			$haya_favorite_user_html = ob_get_clean();
			
			$haya_favorite_msg = array(
				'count' => $haya_favorite_count,
				'users' => $haya_favorite_user_html,
				'msg' => lang('haya_favorite_user_favorite_success_tip'),
			);
			
			

if (function_exists("notice_send")) {
	
	
	
	$thread = thread_read($thread['tid']);
	$thread['subject'] = notice_substr($thread['subject'], 20);
	
	$notice_thread_subject = $thread['subject'];
	$notice_thread_substr_subject = htmlspecialchars(strip_tags($thread['subject']));
	$notice_thread_substr_subject = notice_substr($notice_thread_substr_subject, 20);
	$notice_thread_url = url('thread-'.$thread['tid']);
	$notice_thread = '<a target="_blank" href="'.$notice_thread_url.'">《'.$notice_thread_subject.'》</a>';
	
	$notice_user_url = url('user-'.$user['uid']);
	$notice_user_avatar_url = $user['avatar_url'];
	$notice_user_username = $user['username'];
	$notice_user = '<a href="'.$notice_user_url.'" target="_blank"><img class="avatar-1" src="'.$notice_user_avatar_url.'"> '.$notice_user_username.'</a>';
	
	
	
	$notice_msg = str_replace(
		array(
			'{thread_subject}', '{thread_substr_subject}', '{thread_url}', '{thread}', 
			'{user_url}', '{user_avatar_url}', '{user_username}', '{user}'
		),
		array(
			$notice_thread_subject, $notice_thread_substr_subject, $notice_thread_url, $notice_thread, 
			$notice_user_url, $notice_user_avatar_url, $notice_user_username, $notice_user
		),
		lang('haya_favorite_send_notice_for_thread')
	);
	notice_send($user['uid'], $thread['uid'], $notice_msg, 155);

					
}


			
			message(1, $haya_favorite_msg);
		} elseif ($action == 'delete') {
			
			
			if (empty($haya_check_favorite)) {
				message(0, lang('haya_favorite_user_no_favorite_error_tip'));
			}
			
			haya_favorite_delete_by_tid_and_uid($tid, $user['uid']);
			
			if (isset($haya_favorite_config['favorite_count_type']) 
				&& $haya_favorite_config['favorite_count_type'] == 1
			) {
				$haya_favorite_count = haya_favorite_count(array('tid' => $tid));
				
				thread__update($tid, array('favorites' => $haya_favorite_count));
			} else {
				$haya_favorite_count = MAX(0, $thread['favorites'] - 1);
				
				haya_favorite_thread_user_favorites($tid, -1);
			}

			// 更新当前用户收藏数
			$haya_favorite_user_now_favorite_count = haya_favorite_count(array('uid' => $uid));
			user__update($uid, array('favorites' => $haya_favorite_user_now_favorite_count));
			
			$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
			ob_start();
			include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
			$haya_favorite_user_html = ob_get_clean();
			
			$haya_favorite_msg = array(
				'count' => $haya_favorite_count,
				'users' => $haya_favorite_user_html,
				'msg' => lang('haya_favorite_user_delete_favorite_success_tip'),
			);
			
			
			
			message(1, $haya_favorite_msg);
		}
		
	}

} elseif ($action == 'favorites') {
	
	$header['title'] = lang('haya_favorite_my_favorite') . " - " . $conf['sitename'];
	
	$haya_favorite_config = setting_get('haya_favorite');
	
	if (strtolower($haya_favorite_config['user_favorite_sort']) == 'asc') {
		$user_favorite_sort = 'asc';
	} else {
		$user_favorite_sort = 'desc';
	}
	
	$orderby = param('orderby', $user_favorite_sort);
	if (strtolower($orderby) == 'asc') {
		$orderby_config = array('create_date' => 1);
	} else {
		$orderby_config = array('create_date' => -1);
	}
	
	$pagesize = intval($haya_favorite_config['user_favorite']);
	$page = param(2, 1);
	$cond['uid'] = $uid; 
	
	$haya_favorite_count = haya_favorite_count($cond);
	$threadlist = haya_favorite_find($cond, $orderby_config, $page, $pagesize);
	$pagination = pagination(url("my-favorites-{page}", array("orderby" => $orderby)), $haya_favorite_count, $page, $pagesize);
	
	include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorites.htm');	
}




elseif ($action == 'post_like') {
	
	if (isset($haya_post_like_config['open_my_post_like'])
		&& $haya_post_like_config['open_my_post_like'] != 1
	) {
		message(-1, lang('haya_post_like_my_no_post_like_tip'));
	}

	$pagesize = intval($haya_post_like_config['my_post_like_pagesize']);
	$page = param(2, 1);
	$cond['uid'] = $uid; 
	
	$haya_post_like_count = haya_post_like_count($cond);
	$haya_post_like_post_list = haya_post_like_find($cond, array('create_date' => -1), $page, $pagesize);
	if (!empty($haya_post_like_post_list)) {
		foreach ($haya_post_like_post_list as & $haya_post_like_post_value) {
			$haya_post_like_post_value['thread'] = thread_read_cache($haya_post_like_post_value['tid']);
		}
	}
	
	$pagination = pagination(url("my-post_like-{page}"), $haya_post_like_count, $page, $pagesize);
	
	include _include(APP_PATH.'plugin/haya_post_like/view/htm/my_post_like.htm');
}





?>