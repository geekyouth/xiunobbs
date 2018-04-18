<?php
exit;

$haya_post_info_config = GLOBALS('haya_post_info_config');

if ((isset($haya_post_info_config['show_see_him']) 
	&& $haya_post_info_config['show_see_him'] == 1)
	|| (isset($haya_post_info_config['show_see_first_floor']) 
	&& $haya_post_info_config['show_see_first_floor'] == 1)
	|| (isset($haya_post_info_config['show_post_sort']) 
	&& $haya_post_info_config['show_post_sort'] == 1)
) {

	$thread = GLOBALS('thread');

	if (!empty($thread)) {
		
		if ((isset($haya_post_info_config['show_see_him']) 
			&& $haya_post_info_config['show_see_him'] == 1)
			|| (isset($haya_post_info_config['show_see_first_floor']) 
			&& $haya_post_info_config['show_see_first_floor'] == 1)
		) {
			$haya_post_info_see_user = _REQUEST('user');
		} else {
			$haya_post_info_see_user = '';
		}
		
		if ((isset($haya_post_info_config['show_post_sort']) 
			&& $haya_post_info_config['show_post_sort'] == 1)
		) {
			$haya_post_info_post_default_sort = isset($haya_post_info_config['post_default_sort']) ? trim($haya_post_info_config['post_default_sort']) : '';
			$haya_post_info_orderby = _REQUEST('sort', $haya_post_info_post_default_sort);
		} else {
			$haya_post_info_orderby = 'asc';
		}

		if (strtolower($haya_post_info_orderby) == 'desc') {
			
			$haya_post_info_orderby = array('pid' => -1);
			
			$haya_post_info_cond = array('tid' => $tid);

			if (!empty($haya_post_info_see_user)) {
				$haya_post_info_cond['uid'] = intval($haya_post_info_see_user);
			}
			
			$postlist = post__find($haya_post_info_cond, $haya_post_info_orderby, $page, $pagesize);
			
			if ($page == 1) {
				$first_thread = post__read($thread['firstpid']);
				$postlist += array($thread['firstpid'] => $first_thread);
			}
			
			if (!empty($postlist)) {
				$floor = $thread['posts'] - ($page - 1) * $pagesize + 1;
				foreach ($postlist as & $post) {
					$post['floor'] = $floor--;
					post_format($post);
				}
			}
			
			return $postlist;	

		} elseif (!empty($haya_post_info_see_user)) {
			$haya_post_info_cond = array('tid' => $tid, 'uid' => intval($haya_post_info_see_user));
			
			$postlist = post__find($haya_post_info_cond, array('pid' => 1), $page, $pagesize);
			if ($page == 1) {
				$first_thread = post__read($thread['firstpid']);
				$postlist += array($thread['firstpid'] => $first_thread);
			}
			
			if (!empty($postlist)) {
				$floor = ($page - 1) * $pagesize + 1;
				foreach ($postlist as & $post) {
					$post['floor'] = $floor++;
					post_format($post);
				}
			}
			
			return $postlist;
		}
	}

}
	
?>
