<?php
exit;

// 更新帖子点赞统计
$haya_post_like_user_tids_count = haya_post_like_count(array('uid' => $uid));
$haya_post_like_user_tids = haya_post_like_find_tids_by_uid($uid, $haya_post_like_user_tids_count);

$haya_post_like_count = post_count(array('tid' => $haya_post_like_user_tids));
$haya_post_like_postlist = post__find(array('tid' => $haya_post_like_user_tids), array('pid' => -1), 1, $haya_post_like_count);
if (!empty($haya_post_like_postlist)) {
	foreach ($haya_post_like_postlist as $haya_post_like_post) {
		if (isset($haya_post_like_post['likes']) && $haya_post_like_post['likes'] > 0) {
			post__update($haya_post_like_post['pid'], array('likes-' => 1));
		}
		
		if ($haya_post_like_post['isfirst'] == 1) {
			$haya_post_like_thread = thread__read($haya_post_like_post['tid']);
			
			if (isset($haya_post_like_thread['likes']) && $haya_post_like_thread['likes'] > 0) {
				thread__update($haya_post_like_post['tid'], array('likes-' => 1));
			}
		}
	}
}

haya_post_like_delete_by_uid($uid);

?>