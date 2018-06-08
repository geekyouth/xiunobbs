<?php

function get_forum_list() {
    $data = db_find('forum', [], ['fid' => 1], 1, 10, '', ['fid', 'name']);
    return $data;
}

/** 通过板块id获得tag信息 **/
function get_tag_by_fid($fid) {
    $data = db_find_one('tag_cate', ['fid' => $fid], [], ['cateid']); // 只找了一个分类，这个必然要是部门
    $data = db_find('tag', ['cateid' => $data['cateid']], [], 1, 10, '', ['tagid', 'name']); // 这个只找了10条记录，如果有更多，改写10
    return $data;
}

function get_tagname_by_tagid($tagid) {
	$data = db_find_one('tag', ['tagid' => $tagid], [], ['name']);
	return $data;
}

/** 组装帖子详情页里面的图片html内容 */
function thread_images_html($tid) {
    $pid = db_find_one('post', ['tid' => $tid, 'isfirst' => 1], [], ['pid']);
    $data = db_find('attach', ['pid' => $pid['pid']], ['aid' => 1]);
    $html = '';
    if ($data) {
    	foreach($data as $item) {
    		$html .= '<div><img src="./upload/attach/' . $item['filename'] . '"></div>';
    	}
    }
    
    return $html;
}

/** 根据postid获得图片详情 */
function post_images_html($pid) {
    $data = db_find('attach', ['pid' => $pid], ['aid' => 1]);

    $html = '';
    if ($data) {
        foreach($data as $item) {
            $html .= '<div><img src="./upload/attach/' . $item['filename'] . '"></div>';
        }
    }

    return $html;
}

function get_forum_and_tag() {
    $forum = get_forum_list();
    foreach($forum as &$_forum) {
        $_forum['tags'] = get_tag_by_fid($_forum['fid']);
    }
    return $forum;
}

function get_tag_by_tagid($tagid) {
    $tag = db_find_one('tag', ['tagid' => $tagid], [], ['tagid', 'name']);
    if(!$tag) {
        $tag = [
            'tagid' => 0,
            'name'  => '全部'
        ];
    }
    return $tag;
}

function get_count_by_tagid($tagid) {
    $threads = db_find('tag_thread', ['tagid' => $tagid], [], 1, 1000);
    return $threads;
}

function get_today_by_tids($tids) {
    $db = $_SERVER['db'];
    $tids = "($tids)";
    $todayStart = strtotime(date("Y-m-d"),time());
    $todayEnd   = $todayStart + 86399;
    $sql = "SELECT * FROM bbs_thread WHERE create_date > $todayStart AND create_date < $todayEnd AND tid IN $tids";
    $threads = $db->sql_find($sql);
    return $threads;
}