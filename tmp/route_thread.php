<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);



if ($action == 'favorite') {

	$header['title'] = lang('haya_favorite') . " - " . $conf['sitename'];

	$haya_favorite_config = setting_get('haya_favorite');
	
	if ($method == 'POST') {

		$tid = param('tid');

		$thread = thread_read($tid);
		empty($thread) AND message(0, lang('thread_not_exists'));
		$haya_check_favorite = haya_favorite_find_by_uid_and_tid($uid, $tid);
		
		$haya_favorite_user_favorite_count = isset($haya_favorite_config['user_favorite_count']) ? intval($haya_favorite_config['user_favorite_count']) : 20;
		
		$haya_favorite_users = haya_favorite_find_by_tid($tid, $haya_favorite_user_favorite_count);
		
		ob_start();
		include _include(APP_PATH.'plugin/haya_favorite/view/htm/my_favorite_users.htm');	
		$haya_favorite_user_html = ob_get_clean();

		message(1, $haya_favorite_user_html);
	}
	
	message(1, lang('haya_favorite_error'));

} else




// 发表主题帖 | create new thread
if($action == 'create') {
	
	
		
	user_login_check();

	if($method == 'GET') {
		
		
		
		$fid = param(2, 0);
		$forum = $fid ? forum_read($fid) : array();
		
		$forumlist_allowthread = forum_list_access_filter($forumlist, $gid, 'allowthread');
		$forumarr = xn_json_encode(arrlist_key_values($forumlist_allowthread, 'fid', 'name'));
		if(empty($forumlist_allowthread)) {
			message(-1, lang('user_group_insufficient_privilege'));
		}
		
		$header['title'] = lang('create_thread');
		$header['mobile_title'] = $fid ? $forum['name'] : '';
		$header['mobile_linke'] = url("forum-$fid");
		
		if(IS_MOBILE) {
	$is_thread_create = true;
	include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/post.htm');
	exit();
}
		
		include _include(APP_PATH.'view/htm/post.htm');
		
	} else {
		
		

$kv_vcode = kv_get('vcode');
if(!empty($kv_vcode['vcode_thread_create_on'])) {
	$vcode_post = param('vcode');
	$vcode_sess = _SESSION('vcode');
	strtolower($vcode_post) != strtolower($vcode_sess) AND message('vcode', '验证码不正确');
}

		
		$fid = param('fid', 0);
		$forum = forum_read($fid);
		empty($forum) AND message('fid', lang('forum_not_exists'));
		
		$r = forum_access_user($fid, $gid, 'allowthread');
		!$r AND message(-1, lang('user_group_insufficient_privilege'));
		
		$subject = param('subject');
		empty($subject) AND message('subject', lang('please_input_subject'));
		xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength'=>128)));
		
		$message = param('message', '', FALSE);
		empty($message) AND message('message', lang('please_input_message'));
		$doctype = param('doctype', 0);
		$doctype > 10 AND message(-1, lang('doc_type_not_supported'));
		xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));
		
		$thread = array (
			'fid'=>$fid,
			'uid'=>$uid,
			'sid'=>$sid,
			'subject'=>$subject,
			'message'=>$message,
			'time'=>$time,
			'longip'=>$longip,
			'doctype'=>$doctype,
		);
		
		
		qt_check_sensitive_word($thread['subject'], 'post_sensitive_words', $qt_error) AND message('subject', lang('thread_contain_sensitive_word') . $qt_error);
		qt_check_sensitive_word($thread['message'], 'post_sensitive_words', $qt_error) AND message('message', lang('post_contain_sensitive_word') . $qt_error);
		// todo:
		
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
					message(-1, '请选择'.$cate['name']);
				}
			}
			
		}
		

		
		$tid = thread_create($thread, $pid);
		$pid === FALSE AND message(-1, lang('create_post_failed'));
		$tid === FALSE AND message(-1, lang('create_thread_failed'));
		
		
		// todo:
		/*
		$tag_cate_id_arr = param('tag_cate_id', array(0));
		foreach($tag_cate_id_arr as $tag_cate_id => $tagid) {
			tag_thread_create($tagid, $tid);
		}
		*/
		
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
					message(-1, '请选择'.$cate['name']);
				}
			}
			// 判断是否默认
			if($defaulttagid) {
				if(empty($intersect)) {
					array_push($tagids, $defaulttagid);
				}
			}
		}
		
		foreach($tagids as $tagid) {
			$tagid AND tag_thread_create($tagid, $tid);
		}
		

unset($_SESSION['vcode']);
unset($_SESSION['vcode_initpw_ok']);


 message(0, lang('create_thread_sucessfully').$_SESSION['sg_group_message']);
 
		message(0, lang('create_thread_sucessfully'));
	}
	
// 帖子详情 | post detail
} else {
	
	// thread-{tid}-{page}-{keyword}.htm
	$tid = param(1, 0);
	$page = param(2, 1);
	$keyword = param(3);
	$pagesize = $conf['postlist_pagesize'];
	//$pagesize = 10;
	//$page == 1 AND $pagesize++;
	
	
	
	$thread = thread_read($tid);
	empty($thread) AND message(-1, lang('thread_not_exists'));
	
	$fid = $thread['fid'];
	$forum = forum_read($fid);
	empty($forum) AND message(3, lang('forum_not_exists'));
	
	$postlist = post_find_by_tid($tid, $page, $pagesize);
	empty($postlist) AND message(4, lang('post_not_exists'));
	
	if($page == 1) {
		empty($postlist[$thread['firstpid']]) AND message(-1, lang('data_malformation'));
		$first = $postlist[$thread['firstpid']];
		unset($postlist[$thread['firstpid']]);
		$attachlist = $imagelist = $filelist = array();
		
		// 如果是大站，可以用单独的点击服务，减少 db 压力
		// if request is huge, separate it from mysql server
		thread_inc_views($tid);
	} else {
		$first = post_read($thread['firstpid']);
	}
	
	$keywordurl = '';
	if($keyword) {
		$thread['subject'] = post_highlight_keyword($thread['subject'], $keyword);
		//$first['message'] = post_highlight_keyword($first['subject']);
		$keywordurl = "-$keyword";
	}
	$allowpost = forum_access_user($fid, $gid, 'allowpost') ? 1 : 0;
	$allowupdate = forum_access_mod($fid, $gid, 'allowupdate') ? 1 : 0;
	$allowdelete = forum_access_mod($fid, $gid, 'allowdelete') ? 1 : 0;
	
	forum_access_user($fid, $gid, 'allowread') OR message(-1, lang('user_group_insufficient_privilege'));
	
	$pagination = pagination(url("thread-$tid-{page}$keywordurl"), $thread['posts'] + 1, $page, $pagesize);
	
	$header['title'] = $thread['subject'].'-'.$forum['name'].'-'.$conf['sitename']; 
	//$header['mobile_title'] = lang('thread_detail');
	$header['mobile_title'] = $forum['name'];;
	$header['mobile_link'] = url("forum-$fid");
	$header['keywords'] = ''; 
	$header['description'] = $thread['subject'];
	$_SESSION['fid'] = $fid;
	
	
	
	

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



if (isset($haya_post_like_config['open_post'])
	&& $haya_post_like_config['open_post'] == 1
) {
	$hot_like_post_size = intval($haya_post_like_config['hot_like_post_size']) + 1;
	$hot_like_post_low_count = intval($haya_post_like_config['hot_like_post_low_count']);
	
	$haya_post_like_post_ids = array();
	if (!empty($postlist)) {
		foreach ($postlist as $haya_post_like_post) {
			$haya_post_like_post_ids[] = $haya_post_like_post['pid'];
		}
	}
	
	$haya_post_like_life_time = isset($haya_post_like_config['hot_like_life_time']) ? intval($haya_post_like_config['hot_like_life_time']) : 86400;
	$haya_post_like_hot_posts = haya_post_like_find_hot_posts_by_tid_cache($thread['tid'], $hot_like_post_size, $hot_like_post_low_count, $haya_post_like_life_time);
	
	if (!empty($haya_post_like_hot_posts)) {
		if (isset($haya_post_like_config['hot_like_isfirst'])
			&& $haya_post_like_config['hot_like_isfirst'] == 1
		) {
			$hot_like_isfirst = true;
		} else {
			$hot_like_isfirst = false;
		}
		
		$haya_post_like_hot_post_isfirst = false;
		foreach ($haya_post_like_hot_posts as $haya_post_like_hot_post_key => $haya_post_like_hot_post) {
			if ($haya_post_like_hot_post['isfirst'] == 1 && !$hot_like_isfirst) {
				unset($haya_post_like_hot_posts[$haya_post_like_hot_post_key]);
				$haya_post_like_hot_post_isfirst = true;
			} else {
				$haya_post_like_post_ids[] = $haya_post_like_hot_post['pid'];
				
				// 移除楼层
				$haya_post_like_hot_posts[$haya_post_like_hot_post_key]['floor'] = '';
			}
		}
		
		if (!$haya_post_like_hot_post_isfirst && (count($haya_post_like_hot_posts)) >= $hot_like_post_size) {
			array_pop($haya_post_like_hot_posts);
		}
	}
	
	$haya_post_like_pids = haya_post_like_find_by_pids_and_uid($haya_post_like_post_ids, $uid, count($haya_post_like_post_ids));
}


	
	include _include(APP_PATH.'view/htm/thread.htm');
}



?>