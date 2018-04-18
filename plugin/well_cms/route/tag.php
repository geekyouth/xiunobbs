<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

// hook well_tag_start.php

well_set_cookie_forumarr('CMS');

// hook well_tag_before.php

$action = param(1, 'list');
$pagesize = 30;

if ($action == 'list') {

    $header['title'] = lang('well_tag_manage') . '-WellCMS';
    $header['keywords'] = '';
    $header['description'] = lang('well_tag_manage') . '-' . $conf['sitebrief'];

    $page = param(2, 1);

    $arr = well_cms_tag_count_desc_find($page, $pagesize);
    $count = well_cms_tag_count();

    $pagination = pagination(url('tag-list-{page}'), $count, $page, $pagesize);

    // hook well_tag_end.php

    if ($ajax) {
        $arr = well_cms_tag_safe_info($arr);
        message(0, array('taglist' => $arr));
    } else {
        include _include(APP_PATH . './plugin/well_cms/view/htm/tag_list.htm');
    }
} else {
    $tagid = param(1, 0);;
    $page = param(2, 1);

    $r = well_cms_tag_read_tagid($tagid);

    $arr = well_cms_tag_data_find($tagid, $page, $pagesize);
    $tids = arrlist_values($arr, 'tid');
    $count = well_cms_tag_data_tagid_count($tagid);

    if ($tids) {
        $threadlist = well_cms_tag_data_thread_find($tids, 1, $pagesize);
    }

    $pagination = pagination(url('tag-' . $tagid . '-{page}'), $count, $page, $pagesize);

    $header['title'] = $r['name'] . '-WellCMS';
    $header['keywords'] = '';
    $header['description'] = $r['name'] . '-' . $conf['sitebrief'];

    if ($ajax) {
        foreach ($threadlist as &$thread) $thread = thread_safe_info($thread);
        message(0, array('threadlist' => $threadlist));
    } else {
        // hook well_tag_template_htm.php
        include _include(APP_PATH . './plugin/well_cms/view/htm/tag_read.htm');
    }
}

?>