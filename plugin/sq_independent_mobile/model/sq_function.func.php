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

function get_forum_and_tag() {
    $forum = get_forum_list();
    foreach($forum as &$_forum) {
        $_forum['tags'] = get_tag_by_fid($_forum['fid']);
    }
    return $forum;
}