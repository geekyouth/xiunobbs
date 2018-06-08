<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

user_login_check();



if($action == 'update_log_read') {
	
	$logid = param(2, 0);
	$log = post_update_log_read($logid);
	empty($log) AND message(-1, lang('update_logid_not_exists'));
	
	$pid = $log['pid'];
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists:'));
	
	$tid = $post['tid'];
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists:'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists:'));
	
	$fid = $thread['fid'];
	$allowtop = forum_access_mod($fid, $gid, 'allowtop');
	($gid > 5 || !$allowtop) AND message(-1, lang('user_group_insufficient_privilege'));
	
	$header['title'] = lang('post_create');
	$header['mobile_title'] = lang('post_create');
	$header['mobile_link'] = url("thread-$tid");
	
	include _include(APP_PATH.'plugin/xn_mod_enhance/view/htm/post_update_log_read.htm');

// 删除更新日志
} elseif($action == 'update_log_delete') {

	$logid = param(2, 0);
	$log = post_update_log_read($logid);
	empty($log) AND message(-1, lang('update_logid_not_exists'));
	
	$pid = $log['pid'];
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists:'));
	
	$thread = thread_read($post['tid']);
	empty($thread) AND message(-1, lang('thread_not_exists:'));
	
	$fid = $thread['fid'];
	
	// 判断版主权限
	$allowtop = forum_access_mod($fid, $gid, 'allowtop');
	($gid > 5 || !$allowtop) AND message(-1, lang('user_group_insufficient_privilege'));
	
	$r = post_update_log_delete($logid);
	
	message(0, '');

// 更新日志
} elseif($action == 'update_log_list') {
	
	$pid = param(2, 0);
	
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists:'));

	$thread = thread_read($post['tid']);
	empty($thread) AND message(-1, lang('thread_not_exists:'));
	
	$fid = $thread['fid'];
	$allowtop = forum_access_mod($fid, $gid, 'allowtop');
	($gid > 5 || !$allowtop) AND message(-1, lang('user_group_insufficient_privilege'));
	
	$post_update_log_list = array();
	if($allowtop && $post['updates'] > 0) {
		$post_update_log_list = post_update_log_find_by_pid($post['pid']);
	}
	
	include _include(APP_PATH.'plugin/xn_mod_enhance/view/htm/post_update_log_list.htm');
	
}






if($action == 'create') {
	
	$tid = param(2);
	$quick = param(3);
	$quotepid = param(4);
		
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists'));
	
	$fid = $thread['fid'];
	
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists'));
	
	$r = forum_access_user($fid, $gid, 'allowpost');
	if(!$r) {
		message(-1, lang('user_group_insufficient_privilege'));
	}
	
	($thread['closed'] && ($gid == 0 || $gid > 5)) AND message(-1, lang('thread_has_already_closed'));
	
	
		// todo:
		$tagids = tag_thread_find_tagid_by_tid($tid, $forum['tagcatelist']);

	
	if($method == 'GET') {
		
		if(IS_MOBILE) {
    $header['title'] = lang('post_create');
    $header['mobile_title'] = lang('post_create');
    $header['mobile_link'] = url("thread-$tid");

    include _include(APP_PATH . SQ_MOBILE_PATH . 'view/htm/post.htm');
    return;
}
		
		$header['title'] = lang('post_create');
		$header['mobile_title'] = lang('post_create');
		$header['mobile_link'] = url("thread-$tid");

		include _include(APP_PATH.'view/htm/post.htm');
		
	} else {
		
		
		qt_check_sensitive_word($_REQUEST['message'], 'post_sensitive_words', $qt_error) AND message('message', lang('post_contain_sensitive_word') . $qt_error);
		
		$message = param('message', '', FALSE);
		empty($message) AND message('message', lang('please_input_message'));
		
		$doctype = param('doctype', 0);
		xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));
		
		$thread['top'] > 0 AND thread_top_cache_delete();
		
		$quotepid = param('quotepid', 0);
		$quotepost = post__read($quotepid);
		(!$quotepost || $quotepost['tid'] != $tid) AND $quotepid = 0;
		
		$post = array(
			'tid'=>$tid,
			'uid'=>$uid,
			'create_date'=>$time,
			'userip'=>$longip,
			'isfirst'=>0,
			'doctype'=>$doctype,
			'quotepid'=>$quotepid,
			'message'=>$message,
		);
		$pid = post_create($post, $fid, $gid);
		empty($pid) AND message(-1, lang('create_post_failed'));
		
		// thread_top_create($fid, $tid);

		$post = post_read($pid);
		$post['floor'] = $thread['posts'] + 2;
		$postlist = array($post);
		
		$allowpost = forum_access_user($fid, $gid, 'allowpost');
		$allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
		$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
		
		

if (isset($haya_post_info_config['at_user_to_notice']) 
	&& $haya_post_info_config['at_user_to_notice'] == 1
) { 

	if (function_exists("notice_send")) {
		$haya_post_info_pagesize = $conf['postlist_pagesize'];
		$haya_post_info_page = ceil(($thread['posts'] + 1) / $haya_post_info_pagesize);
		$haya_post_info_page = max(1, $haya_post_info_page);
		
		$notice_thread_subject = $thread['subject'];
		$notice_thread_substr_subject = notice_substr($thread['subject'], 20);
		$notice_thread_url = url('thread-'.$thread['tid']);
		$notice_thread = '<a target="_blank" href="'.$notice_thread_url.'">《'.$notice_thread_subject.'》</a>';
		
		$notice_post_message = $post['message'];
		$notice_post_substr_message = notice_substr($post['message'], 40, FALSE);
		$notice_post_url = url('thread-'.$thread['tid'].'-'.$haya_post_info_page).'#'.$post['pid'];
		
		$notice_user_url = url('user-'.$user['uid']);
		$notice_user_avatar_url = $user['avatar_url'];
		$notice_user_username = $user['username'];
		$notice_user = '<a href="'.$notice_user_url.'" target="_blank"><img class="avatar-1" src="'.$notice_user_avatar_url.'"> '.$notice_user_username.'</a>';
		
		$notice_msg_tpl = '<div class="comment-info">在主题 <a target="_blank" href="{thread_url}" title="{thread_subject}">《{thread_substr_subject}》</a> 的回复中提到了你：</div> '
			.'<div class="single-comment pt-1"><a target="_blank" href="{post_url}">{post_substr_message}</a></div>';
		$notice_msg = str_replace(
			array(
				'{thread_subject}', '{thread_substr_subject}', '{thread_url}', '{thread}', 
				'{post_message}', '{post_substr_message}', '{post_url}', 
				'{user_url}', '{user_avatar_url}', '{user_username}', '{user}'
			),
			array(
				$notice_thread_subject, $notice_thread_substr_subject, $notice_thread_url, $notice_thread, 
				$notice_post_message, $notice_post_substr_message, $notice_post_url,  
				$notice_user_url, $notice_user_avatar_url, $notice_user_username, $notice_user
			),
			$notice_msg_tpl
		);
	}

	preg_match_all('#@([a-zA-Z0-9_]+|\W+|[^x00-xff]+)#i', $post['message_fmt'], $uns);
	$cnt = count($uns[1]);
	if ($cnt > 0) {
		for ($i = 0; $i < $cnt; $i++) {
			$u = user_read_by_username($uns[1][$i]);
			if (!$u || empty($u['uid'])) {
				continue;
			}
			
			$post['message_fmt'] = str_replace($uns[0][$i], '<a href="' . url('user-' . $u['uid']) . '" target="_blank"><em>@' . $u['username'] . '</em></a>', $post['message_fmt']);
			
			if (function_exists("notice_send")) {				
				notice_send($user['uid'], $u['uid'], $notice_msg, 156);
			}
		}
	}
	
	post__update($pid, array("message_fmt" => $post['message_fmt']));
}



	$thread['subject'] = notice_substr($thread['subject'], 20);

	// 回复
	$notice_message = '<div class="comment-info"><a class="mr-1 text-grey" href="'.url("thread-$thread[tid]").'#'.$pid.'">'.lang('notice_lang_comment').'</a>'.lang('notice_message_replytoyou').'<a href="'.url("thread-$thread[tid]").'">《'.$thread['subject'].'》</a></div><div class="single-comment"><a href="'.url("thread-$thread[tid]").'#'.$pid.'">'.notice_substr($message, 40, FALSE).'</a></div>';
	$recvuid = $thread['uid'];

	
	$recvuid != $quotepost['uid'] AND notice_send($uid, $recvuid, $notice_message, 2); //$quotepost['uid']可能是null，但不影响逻辑

	// 引用
	if(!empty($quotepid) && $quotepid > 0) {

		

		 
		 $notice_quote_message = '<div class="comment-info"><a class="mr-1 text-grey" href="'.url("thread-$thread[tid]").'#'.$pid.'">'.lang('notice_lang_reply').'</a>'.lang('notice_message_replytoyou_at').'<a href="'.url("thread-$thread[tid]").'">《'.$thread['subject'].'》</a>'.lang('notice_message_replytoyou_for').'</div><div class="quote-comment">'.notice_substr($quotepost['message'], 40, FALSE).'</div><div class="reply-comment"><a href="'.url("thread-$thread[tid]").'#'.$pid.'">'.notice_substr($message, 40, FALSE).'</a></div>';



		notice_send($uid, $quotepost['uid'], $notice_quote_message, 2);	
	}



    foreach($postlist as &$_post) { // 返回的postlist中附加该post的图片附件信息
        $imgs = get_imgs_by_postid($_post['pid']);
        $_post['sq_imgs'] = $imgs;
    }

    $return_html = param('return_html', 0);
    if($return_html) {
        $filelist = array();
        ob_start();
        include _include(APP_PATH . SQ_MOBILE_PATH . 'view/htm/thread_post_list.inc.htm');
        $s = ob_get_clean();
                    
        message(0, $s);
    } else {
        message(0, lang('create_post_sucessfully'));
    }
		$return_html = param('return_html', 0);
		$sg_group = setting_get('sg_group');
		$uid AND user__update($uid, array('credits+'=>$sg_group['post_credits']));
		user_update_group($uid);
		if($return_html) {
			$filelist = array();
			ob_start();
			$sg_group_pid = $pid;
			include _include(APP_PATH.'view/htm/post_list.inc.htm');
			$s = ob_get_clean();
			message(0, $s);
		} else {
			
			message(0, lang('create_post_sucessfully').lang('sg_creditsplus',  array('credits'=>$sg_group['post_credits'])));
		}

		
		// 直接返回帖子的 html
		// return the html string to browser.
		$return_html = param('return_html', 0);
		if($return_html) {
			$filelist = array();
			ob_start();
			include _include(APP_PATH.'view/htm/post_list.inc.htm');
			$s = ob_get_clean();
						
			message(0, $s);
		} else {
			message(0, lang('create_post_sucessfully'));
		}
	
	}
	
} elseif($action == 'update') {

	$pid = param(2);
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists:'));
	
	$tid = $post['tid'];
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists:'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists:'));
	
	$isfirst = $post['isfirst'];
	
	!forum_access_user($fid, $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));
	$allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
	!$allowupdate AND !$post['allowupdate'] AND message(-1, lang('have_no_privilege_to_update'));
	!$allowupdate AND $thread['closed'] AND message(-1, lang('thread_has_already_closed'));
	
	
	
	if($method == 'GET') {
		
		
		
		$forumlist_allowthread = forum_list_access_filter($forumlist, $gid, 'allowthread');
		$forumarr = xn_json_encode(arrlist_key_values($forumlist_allowthread, 'fid', 'name'));
		
		// 如果为数据库减肥，则 message 可能会被设置为空。
		// if lost weight for the database, set the message field empty.
		$post['message'] = htmlspecialchars($post['message'] ? $post['message'] : $post['message_fmt']);
		
		$attachlist = $imagelist = $filelist = array();
		if($post['files']) {
			list($attachlist, $imagelist, $filelist) = attach_find_by_pid($pid);
		}
		
		if(IS_MOBILE) {
	// 处理一个$filelistSq 的问题
	list($attachlist, $imagelist, $filelist) = attach_find_by_pid($pid); // 直接让其赋值
	$filelistSq = $imagelist;
	if(!$post['flies']) {
		$attachlist = $filelist = [];
	}

	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/post.htm');
	return;
}
		// todo:
		$tagids = tag_thread_find_tagid_by_tid($tid, $forum['tagcatelist']);
				
			// 编辑器支持 HTML 编辑
			if($post['doctype'] == 1) {
				$post['message'] = htmlspecialchars($post['message_fmt']);
			}
		
		include _include(APP_PATH.'view/htm/post.htm');
		
	} elseif($method == 'POST') {
		
		$subject = htmlspecialchars(param('subject', '', FALSE));
		$message = param('message', '', FALSE);
		$doctype = param('doctype', 0);
		
		
		qt_check_sensitive_word($subject, 'post_sensitive_words', $qt_error) AND message('subject', lang('thread_contain_sensitive_word') . $qt_error);
		qt_check_sensitive_word($message, 'post_sensitive_words', $qt_error) AND message('message', lang('post_contain_sensitive_word') . $qt_error);
		
		empty($message) AND message('message', lang('please_input_message'));
		mb_strlen($message, 'UTF-8') > 2048000 AND message('message', lang('message_too_long'));
		
		$arr = array();
		if($isfirst) {
			$newfid = param('fid');
			$forum = forum_read($newfid);
			empty($forum) AND message('fid', lang('forum_not_exists'));
			
			if($fid != $newfid) {
				!forum_access_user($fid, $gid, 'allowthread') AND message(-1, lang('user_group_insufficient_privilege'));
				$post['uid'] != $uid AND !forum_access_mod($fid, $gid, 'allowupdate') AND message(-1, lang('user_group_insufficient_privilege'));
				$arr['fid'] = $newfid;
			}
			if($subject != $thread['subject']) {
				mb_strlen($subject, 'UTF-8') > 80 AND message('subject', lang('subject_max_length', array('max'=>80)));
				$arr['subject'] = $subject;
			}
			$arr AND thread_update($tid, $arr) === FALSE AND message(-1, lang('update_thread_failed'));
		}
		$r = post_update($pid, array('doctype'=>$doctype, 'message'=>$message));
		$r === FALSE AND message(-1, lang('update_post_failed'));
		
		



// 增加一条编辑历史 $pid, $uid, $time, $message
$update_reason = param('update_reason');
$logid = post_update_log_create(array(
	'pid'=>$pid,
	'uid'=>$uid,
	'create_date'=>$time,
	'reason'=>$update_reason,
	'message'=>$post['message_fmt'],
));


$logid AND post__update($pid, array('updates+'=>1, 'last_update_uid'=>$uid, 'last_update_date'=>$time, 'last_update_reason'=>$update_reason));


		// todo:
		/*
		$tag_cate_id_arr = param('tag_cate_id', array(0));
		
		
		$tagids_new = array_values($tag_cate_id_arr);
		$tagids_old = tag_thread_find_tagid_by_tid($tid);
		//print_r($tagids_new);print_r($tagids_old);exit;
		//新增的、删除的 
		$tag_id_delete = array_diff($tagids_old, $tagids_new);
		$tag_id_add = array_diff($tagids_new, $tagids_old);
		foreach($tag_id_delete as $tagid) {
			tag_thread_delete($tagid, $tid);
		}
		foreach($tag_id_add as $tagid) {
			tag_thread_create($tagid, $tid);
		}
		thread_update($tid, array('tagids'=>'', 'tagids_time'=>0));

		*/
		
		if($isfirst) {
			$tagids = param('tagid', array(0));
			$tagcatemap = $forum['tagcatemap'];
			foreach($forum['tagcatemap'] as $cate) {
				$defaulttagid = $cate['defaulttagid'];
				$isforce = $cate['isforce'];
				$catetags = array_keys($cate['tagmap']);
				$intersect = array_intersect($catetags, $tagids); // 比较数组交集
				// 判断是否强制
				if($isforce) {
					if(empty($intersect)) {
						message(-1, '请选择 ['.$cate['name'].']');
					}
				}
				// 判断是否默认
				if($defaulttagid) {
					if(empty($intersect)) {
						array_push($tagids, $defaulttagid);
					}
				}
				
			}
			
			$tagids = array_diff($tagids, array(0));
			$tagids_new = $tagids;
			$tagids_old = tag_thread_find_tagid_by_tid($tid, $forum['tagcatelist']);
			$tag_id_delete = array_diff($tagids_old, $tagids_new);
			$tag_id_add = array_diff($tagids_new, $tagids_old);
			if($tag_id_delete) {
				foreach($tag_id_delete as $tagid) {
					$tagid AND tag_thread_delete($tagid, $tid);
				}
			}
			if($tag_id_add) {
				foreach($tag_id_add as $tagid) {
					$tagid AND tag_thread_create($tagid, $tid);
				}
			}
			thread_update($tid, array('tagids'=>'', 'tagids_time'=>0));
			/*
			foreach($tagids as $tagid) {
				$tagid AND tag_thread_create($tagid, $tid);
			}*/
		}

		
		message(0, lang('update_successfully'));
		//message(0, array('pid'=>$pid, 'subject'=>$subject, 'message'=>$message));
	}
	
} elseif($action == 'delete') {

	$pid = param(2, 0);
	
	
	
	if($method != 'POST') message(-1, lang('method_error'));
	
	$post = post_read($pid);
	empty($post) AND message(-1, lang('post_not_exists'));
	
	$tid = $post['tid'];
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(-1, lang('forum_not_exists'));
	
	$isfirst = $post['isfirst'];
	
	!forum_access_user($fid, $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));
	$allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
	!$allowdelete AND !$post['allowdelete'] AND message(-1, lang('insufficient_delete_privilege'));
	!$allowdelete AND $thread['closed'] AND message(-1, lang('thread_has_already_closed'));
	
	

	if($isfirst) {
		thread_delete($tid);
	} else {
		post_delete($pid);
		//post_list_cache_delete($tid);
	}
	
	
	
	message(0, lang('delete_successfully'));

}



elseif ($action == 'post_like') {

	$header['title'] = lang('haya_post_like')." - " . $conf['sitename'];
	
	if (!$uid) {
		message(0, lang('haya_post_like_login_like_tip'));
	}
	
	
	
	if ($method == 'POST') {

		$pid = param('pid');

		$post = post_read($pid);
		empty($post) AND message(0, lang('post_not_exists'));

		if ($post['isfirst'] == 1) {
			if (isset($haya_post_like_config['open_thread'])
				&& $haya_post_like_config['open_thread'] != 1
			) {
				message(0, lang('haya_post_like_close_thread_tip'));
			}
		} else {
			if (isset($haya_post_like_config['open_post'])
				&& $haya_post_like_config['open_post'] != 1
			) {
				message(0, lang('haya_post_like_close_post_tip'));
			}
		}
	
		haya_post_like_cache_delete($post['tid']);
		
		$haya_post_like_check = haya_post_like_find_by_uid_and_pid($uid, $pid);
		
		$action2 = param(2, 'create');
		if ($action2 == 'create') {
			
			
			if (!empty($haya_post_like_check)) {
				message(0, lang('haya_post_like_user_has_like_tip'));
			}
			
			haya_post_like_create(array(
				'tid' => $post['tid'], 
				'pid' => $pid, 
				'uid' => $user['uid'],
				'create_date' => time(),
				'create_ip' => $longip,
			));			
			
			if (isset($haya_post_like_config['post_like_count_type'])
				&& $haya_post_like_config['post_like_count_type'] == 1
			) {
				$haya_post_like_count = haya_post_like_count(array('pid' => $pid));
				
				post__update($post['pid'], array('likes' => $haya_post_like_count));
				
				if ($post['isfirst'] == 1) {
					thread__update($post['tid'], array('likes' => $haya_post_like_count));
				}
			} else {
				$haya_post_like_count = intval($post['likes']) + 1;
				
				haya_post_like_loves($pid, 1);
				
				if ($post['isfirst'] == 1) {
					thread__update($post['tid'], array('likes+' => 1));
				}
			}
			
			$haya_post_like_msg = array(
				'count' => intval($haya_post_like_count),
				'msg' => lang('haya_post_like_like_success_tip'),
			);
			
			

if (function_exists("notice_send")) {
	
	
	$notice_user = '<a href="'.url('user-'.$user['uid']).'" target="_blank"><img class="avatar-1" src="'.$user['avatar_url'].'"> '.$user['username'].'</a>';

	$thread = thread_read($post['tid']);
	$thread['subject'] = notice_substr($thread['subject'], 20);
	$notice_thread = '<a target="_blank" href="'.url('thread-'.$post['tid']).'">《'.$thread['subject'].'》</a>';

	$post['message'] = htmlspecialchars(strip_tags($post['message']));
	$post['message'] = notice_substr($post['message'], 40);
	$notice_post = '<a target="_blank" href="'.url('thread-'.$post['tid'].'-1').'#'.$post['pid'].'">【'.$post['message'].'】</a>';
	
	if ($post['isfirst'] == 1) {
		$notice_msg_tpl = lang('haya_post_like_send_notice_for_thread');
		
		
	} else {
		$notice_msg_tpl = lang('haya_post_like_send_notice_for_post');
		
		
	}
	
	$notice_msg = str_replace(
		array('{thread}', '{post}', '{user}'),
		array($notice_thread, $notice_post, $notice_user),
		$notice_msg_tpl
	);

	notice_send($user['uid'], $post['uid'], $notice_msg, 150);
	
	
}


			
			message(1, $haya_post_like_msg);
		} elseif ($action2 == 'delete') {
			
			
			if (isset($haya_post_like_config['like_is_delete'])
				&& $haya_post_like_config['like_is_delete'] != 1
			) {
				message(0, lang('haya_post_like_no_unlike_tip'));
			}
			
			if (empty($haya_post_like_check)) {
				message(0, lang('haya_post_like_user_no_like_tip'));
			}
			
			$post_like = haya_post_like_read_by_uid_and_pid($uid, $pid);

			$delete_time = intval($haya_post_like_config['delete_time']);
			if ($post_like['create_date'] + $delete_time > time()) {
				message(0, lang('haya_post_like_no_fast_like_tip'));
			}
			
			haya_post_like_delete_by_pid_and_uid($pid, $user['uid']);
			
			if (isset($haya_post_like_config['post_like_count_type'])
				&& $haya_post_like_config['post_like_count_type'] == 1
			) {
				$haya_post_like_count = haya_post_like_count(array('pid' => $pid));
				
				post__update($post['pid'], array('likes' => $haya_post_like_count));
				
				if ($post['isfirst'] == 1) {
					thread__update($post['tid'], array('likes' => $haya_post_like_count));
				}
			} else {
				$haya_post_like_count = MAX(0, intval($post['likes']) - 1);
				
				haya_post_like_loves($pid, -1);
				
				if ($post['isfirst'] == 1) {
					$haya_post_like_thread = thread__read($post['tid']);
					
					if ($haya_post_like_thread['likes'] > 0) {
						thread__update($post['tid'], array('likes-' => 1));
					}
				}
			}			
			
			$haya_post_like_msg = array(
				'count' => intval($haya_post_like_count),
				'msg' => lang('haya_post_like_unlike_success_tip'),
			);
			
			
			
			message(1, $haya_post_like_msg);
		}
		
		
		
		message(0, lang('haya_post_like_like_error_tip'));	
	}
	
	
	
	message(0, lang('haya_post_like_like_error_tip'));

}




?>