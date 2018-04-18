<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$setting_route = param(3, 'list');

// hook well_content_setting_source_start.php

if ($setting_route == 'list') {
    // 设置后台
    $header['title'] = lang('well_source');
    $header['mobile_title'] = lang('well_source');
    $header['mobile_link'] = url('content-setting-source-list');

    $page = param(4, 1);
    $pagesize = 30;

    $arr = well_cms_source_find($page, $pagesize);
    $count = well_cms_source_count();

    $pagination = pagination(url('content-setting-source-list-{page}'), $count, $page, $pagesize);

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_source_list.htm');

} elseif ($setting_route == 'delete') {

    $id = param(4, 0);
    $r = well_cms_source_read($id);
    empty($r) AND message(-1, jump(lang('well_source_error'), url('content-setting-source-list'), 2));

    $r['count'] AND message(-1, jump(lang('well_delete_source_error'), url('content-setting-source-list'), 2));

    // hook well_content_setting_source_delete_before.php

    well_cms_source_delete($id);

    $arr = well_admin_get_cache_sources();
    $arr = well_array_key_delete($arr, $id);
    well_admin_set_cache_sources($arr);

    // hook well_content_setting_source_delete_after.php

    message(0, jump(lang('well_delete_successfully'), url('content-setting-source-list'), 0));

} elseif ($setting_route == 'post') {
    if ($method == 'POST') {

        $id = param('id', 0);
        $name = param('name', '', TRUE, TRUE);
        $link = param('link', '', TRUE, TRUE);
        if ($id) {
            $update_name = param('update_name', '', TRUE, TRUE);
            if (!$update_name) message(-1, lang('well_source_empty'));

            $link = param('update_link', '', TRUE, TRUE);
            //if (!$link) message(-1, lang('well_source_link_empty'));

            $name = param('old_name', '', TRUE, TRUE);
            if ($name != $update_name) well_cms_source_read_name($update_name) AND message(-1, lang('well_source_repeat'));

            $name = $update_name;
        } else {
            well_cms_source_read_name($name) AND message(-1, lang('well_source_repeat'));
        }

        $arr = array('name' => $name, 'link' => $link);

        $cachearr = array();
        $cachearr = well_admin_get_cache_sources();

        if ($id) {
            // 编辑
            well_cms_source_update($id, $arr);
            isset($cachearr[$id]) AND $cachearr[$id]['name'] = $name;
        } else {
            // 创建
            well_cms_source_create($arr);
            // 加入缓存
            $cachearr[$id] = array('id' => $id, 'name' => $name, 'rank'=> 0);
        }

        // 缓存
        well_admin_set_cache_sources($cachearr);

        message(0, lang('well_operate_success'));
    }
    exit('Access Denied.');
}

// hook well_content_setting_source_end.php

?>