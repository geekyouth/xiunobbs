<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

// hook well_channel_start.php

$fid = param(1, 0);

$forum = forum_read($fid);
empty($forum) AND message(3, lang('forum_not_exists'));
forum_access_user($fid, $gid, 'allowread') OR message(-1, lang('insufficient_visit_forum_privilege'));

$header['title'] = $forum['seo_title'] ? $forum['seo_title'] : $forum['name'] . '-' . $conf['sitename'];
$header['mobile_title'] = $forum['name'];
$header['mobile_link'] = well_nav_url_format($forum);
$header['keywords'] = '';
$header['description'] = $forum['brief'];
well_set_cookie_forumarr('CMS');
$_SESSION['fid'] = $fid;

$well_forum = well_cms_web_channel_data($fid, $forumlist);

$slide = array_value($well_forum, 'slide');
$headline = array_value($well_forum, 'headline');
$guide = array_value($well_forum, 'guide');
$recommend = array_value($well_forum, 'recommend');
$forums = array_value($well_forum, 'thread');

// 轮播凑整 双列排版 防止错版 单一列注释该代码
if ($slide) {
    if (count($slide) % 2 != 0) {
        $i = 0;
        foreach ($slide as $key => &$_thread) {
            $i++;
            if ($i == 1) {
                $slide[] = $_thread;
            }
        }
    }
}

$well_tag = well_cms_web_hot_tag();

// hook well_channel_end.php

if ($ajax) {
    message(0, array('nav' => $well_nav, 'forum' => $well_forum, 'tag' => $well_tag));
} else {
    // hook well_channel_template_htm.php
    include _include(well_cms_template_htm(2, $forum));
    exit();
}
?>