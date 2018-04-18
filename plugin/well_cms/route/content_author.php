<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$setting_route = param(3, 'list');

// hook well_content_setting_author_start.php

if ($setting_route == 'list') {
    // 设置后台
    $header['title'] = lang('well_author');
    $header['mobile_title'] = lang('well_author');
    $header['mobile_link'] = url('content-setting-author-list');

    $page = param(4, 1);
    $pagesize = 30;

    $arr = well_cms_all_author_find($page, $pagesize);
    $count = well_cms_author_count();

    $pagination = pagination(url('content-setting-author-list-{page}'), $count, $page, $pagesize);

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_author_list.htm');

} elseif ($setting_route == 'delete') {

    $id = param(4, 0);
    $r = well_cms_author_read($id);
    empty($r) AND message(-1, jump(lang('well_author_error'), url('content-setting-author-list'), 2));

    $r['count'] AND message(-1, jump(lang('well_delete_author_error'), url('content-setting-author-list'), 2));

    // hook well_content_setting_author_delete_before.php

    well_cms_author_delete($id);

    $arr = well_admin_get_cache_authors();
    $arr = well_array_key_delete($arr, $id);
    well_admin_set_cache_authors($arr);

    // hook well_content_setting_author_delete_after.php

    message(0, jump(lang('well_delete_successfully'), url('content-setting-author-list'), 0));

} elseif ($setting_route == 'post') {
    if ($method == 'POST') {

        $id = param('id', 0);
        $name = param('name', '', TRUE, TRUE);
        if ($id) {
            $name = param('update_name', '', TRUE, TRUE);
            if (!$name) message(-1, lang('well_author_empty'));
        }

        $r = well_cms_author_read_name($name);
        if (!empty($r)) message(-1, lang('well_author_repeat'));
        $arr = array('name' => $name);

        $cachearr = array();
        $cachearr = well_admin_get_cache_authors();

        if ($id) {
            // 编辑
            well_cms_author_update($id, $arr);
            //更新缓存
            isset($cachearr[$id]) AND $cachearr[$id]['name'] = $name;
        } else {
            // 创建
            $id = well_cms_author_create($arr);
            // 加入缓存
            $cachearr[$id] = array('id' => $id, 'name' => $name, 'rank'=> 0);
        }

        // 缓存
        well_admin_set_cache_authors($cachearr);

        message(0, lang('well_operate_success'));
    }
    exit('Access Denied.');
}

// hook well_content_setting_author_end.php

?>