<?php
exit;

// 更新帖子收藏统计

$haya_favorite_user_tids_count = haya_favorite_count(array('uid' => $uid));
$haya_favorite_user_tids = haya_favorite_find_tids_by_uid($uid, $haya_favorite_user_tids_count);

$haya_favorite_thread_count = thread_count(array('tid' => $haya_favorite_user_tids));
$haya_favorite_thread_list = thread__find(array('tid' => $haya_favorite_user_tids), array('tid' => -1), 1, $haya_favorite_thread_count);
if (!empty($haya_favorite_thread_list)) {
	foreach ($haya_favorite_thread_list as $haya_favorite_thread) {
		if (isset($haya_favorite_thread['favorites']) && $haya_favorite_thread['favorites'] > 0) {
			thread__update($haya_favorite_thread['tid'], array('favorites-' => 1));
		}
	}
}

haya_favorite_delete_by_uid($uid);	

?>