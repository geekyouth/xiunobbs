<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$gid != 1 AND message(-1, lang('insufficient_visit_forum_privilege'));

$setting_route = param(3, 'list');

// hook well_content_setting_column_start.php

if ($setting_route == 'list') {
    // 设置后台
    $header['title'] = lang('well_content_column_management_list');
    $header['mobile_title'] = lang('well_content_column_management_list');
    $header['mobile_link'] = url('content-setting-column-list');

    $forumarr = well_cms_get_category_tree($forumlist);

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_column_list.htm');

} elseif ($setting_route == 'create') {

    $header['title'] = lang('well_content_column_create');
    $header['mobile_title'] = lang('well_content_column_create');
    $header['mobile_link'] = url('content-setting-column-create');

    // hook well_content_setting_column_create_start.php
    //$maxfid = forum_maxid();

    foreach ($grouplist as $group) {
        $accesslist[$group['gid']] = $group; // 字段名相同，直接覆盖。 / same field, directly overwrite
    }

    $action = url('content-setting-column-post');
    $name = '';
    $brief = '';
    $modnames = '';
    $well_type = 1;
    $well_forum_type = '';

    $threads = 0;
    $well_son = 0;
    $well_fup = param(4, 0);
    // 所属频道
    $fidarr = well_get_channellist($forumlist);
    $well_channel_name = lang('well_top_column');

    $well_model = '';
    // 版块模型 加载不同模型
    $well_model_arr = array();
    $well_model_arr[] = lang('well_model_radio_news');
    // 其他模型
    // hook well_forum_well_model_create_arr.php

    $well_nav_display = '';
    $well_comment = '';
    $well_tpl = '';
    $well_tpl_class = '';
    $well_tpl_show = '';
    $well_display = '';
    $well_news = '';
    $well_headlines = '';
    $well_channel_display = '';
    $well_channel_slides = '';
    $well_channel_headline = '';
    $well_channel_guide = '';
    $well_channel_recommend = '';
    $well_channel_news = '';
    $well_channel_headlines = '';
    $well_list_news = '';
    $well_list_headlines = '';
    $well_list_recommends = '';
    $well_pagesize = 20;
    $accesson = 0;

    // hook well_content_setting_column_create_end.php

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_column_post.htm');

} elseif ($setting_route == 'update') {

    $header['title'] = lang('well_content_column_edit');
    $header['mobile_title'] = lang('well_content_column_edit');
    $header['mobile_link'] = url('content-setting-column-update');

    // hook well_content_setting_column_update_start.php

    $_fid = param(4, 0);
    $_forum = forum_read($_fid);
    empty($_forum) AND message(-1, lang('forum_not_exists'));

    $accesslist = forum_access_find_by_fid($_fid);

    if (empty($accesslist)) {
        foreach ($grouplist as $group) {
            $accesslist[$group['gid']] = $group; // 字段名相同，直接覆盖。 / same field, directly overwrite
        }
    } else {
        foreach ($accesslist as &$access) {
            $access['name'] = $grouplist[$access['gid']]['name']; // 字段名相同，直接覆盖。 / same field, directly overwrite
        }
    }

    // hook well_content_setting_column_update_before.php

    $action = url('content-setting-column-post');
    $name = $_forum['name'];
    $brief = $_forum['brief'];
    $modnames = well_user_ids_to_names($_forum['moduids']);
    $threads = $_forum['threads'];

    $well_son = $_forum['well_son'];
    $well_type = $_forum['well_type'];
    $well_forum_type = $_forum['well_forum_type'];

    // 有数据禁止修改
    if ($threads || $well_son) {
        if ($well_forum_type) {
            $well_forum_name = lang('well_content_channel');
        } else {
            $well_forum_name = lang('well_top_column');
        }
    }

    $fid = $_forum['fid'];
    $well_fup = $_forum['well_fup'];
    $old_fup = $_forum['well_fup'];
    // 所属频道
    $fidarr = well_get_channellist($forumlist);
    //$well_channel_name = lang('well_top_column');
    $well_channel_name = $fidarr[$_forum['well_fup']];

    $well_model = $_forum['well_model'];
    // 版块模型 加载不同模型
    $well_model_arr = array();
    $well_model_arr[] = lang('well_model_radio_news');
    // 其他模型
    // hook well_forum_well_model_update_arr.php

    $well_nav_display = $_forum['well_nav_display'];
    $well_comment = $_forum['well_comment'];
    $well_tpl = $_forum['well_tpl'];
    $well_tpl_class = $_forum['well_tpl_class'];
    $well_tpl_show = $_forum['well_tpl_show'];
    $well_display = $_forum['well_display'];
    $well_news = $_forum['well_news'];
    $well_headlines = $_forum['well_headlines'];
    $well_channel_display = $_forum['well_channel_display'];
    $well_channel_slides = $_forum['well_channel_slides'];
    $well_channel_headline = $_forum['well_channel_headline'];
    $well_channel_guide = $_forum['well_channel_guide'];
    $well_channel_recommend = $_forum['well_channel_recommend'];
    $well_channel_news = $_forum['well_channel_news'];
    $well_channel_headlines = $_forum['well_channel_headlines'];
    $well_list_news = $_forum['well_list_news'];
    $well_list_headlines = $_forum['well_list_headlines'];
    $well_list_recommends = $_forum['well_list_recommends'];
    $well_pagesize = $_forum['well_pagesize'];
    $accesson = $_forum['accesson'];

    // hook well_content_setting_column_update.php

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_column_post.htm');

} elseif ($setting_route == 'delete') {

    $_fid = param(4, 0);
    $_forum = forum_read($_fid);
    empty($_forum) AND message(-1, jump(lang('forum_not_exists'), url('content-setting-column-list'), 2));

    $system_forum = array(1);
    in_array($_fid, $system_forum) AND message(-1, jump('Not allowed', url('content-setting-column-list'), 2));

    // hook well_content_setting_column_delete_forum_start.php

    if ($_forum['threads']) {
        message(-1, jump(lang('well_delete_thread_before_delete_forum'), url('content-setting-column-list'), 2));
    }

    if ($_forum['well_son']) {
        message(-1, jump(lang('well_please_delete_sub_forum'), url('content-setting-column-list'), 2));
    }

    if ($_forum['well_fup']) {
        $update = array('well_son-' => 1);
        // hook well_content_setting_column_delete_forum_arr.php
        forum_update($_forum['well_fup'], $update);
    }

    forum_delete($_fid);

    forum_list_cache_delete();

    // hook well_content_setting_column_delete_forum_end.php

    message(0, jump(lang('well_delete_successfully'), url('content-setting-column-list'), 0));

} elseif ($setting_route == 'post') {

    if ($method == 'POST') {

        $name = param('name');
        if (!$name) message(-1, lang('well_content_column_error'));

        $rank = param('rank', 0);
        $_fid = param('fid', 0);
        $brief = param('brief', '', FALSE);
        $announcement = param('announcement', '', FALSE);
        $modnames = param('modnames');
        $accesson = param('accesson', 0);
        $moduids = well_user_names_to_ids($modnames);

        // hook well_content_setting_column_post_start.php

        $arr = array(
            'name' => $name,
            'rank' => $rank,
            'brief' => $brief,
            'announcement' => $announcement,
            'moduids' => $moduids,
            'accesson' => $accesson,
        );

        // hook well_content_setting_column_post_arr_start.php

        $well_type = param('well_type', 0);
        $well_nav_display = param('well_nav_display', 0);
        $well_model = param('well_model', 0);
        $well_forum_type = param('well_forum_type', 0);
        $well_fup = param('well_fup', 0);
        $old_fup = param('old_fup', 0);
        $well_tpl = param('well_tpl', '', FALSE);
        $well_tpl_class = param('well_tpl_class', '', FALSE);
        $well_tpl_show = param('well_tpl_show', '', FALSE);

        // 修改前配置上级频道
        if ($old_fup) {
            if ($old_fup != $well_fup) {
                // 旧频道-1
                forum_update($old_fup, array('well_son-' => 1));
                $well_fup AND forum_update($well_fup, array('well_son+' => 1));
            }
        } else {
            $well_fup AND forum_update($well_fup, array('well_son+' => 1));
        }

        // 频道不显示
        if ($well_forum_type == 1) {
            $well_display = 0;
            $well_news = 0;
            $well_channel_display = 0;
            $well_comment = 0;
            $well_headlines = 0;
            $well_channel_news = 0;
            $well_channel_headlines = 0;
            $well_list_news = 0;
            $well_list_headlines = 0;
            $well_list_recommends = 0;

            $well_channel_slides = param('well_channel_slides', 0);
            !$well_channel_slides AND $well_channel_slides = 10;

            $well_channel_headline = param('well_channel_headline', 0);
            !$well_channel_headline AND $well_channel_headline = 10;

            $well_channel_guide = param('well_channel_guide', 0);
            !$well_channel_guide AND $well_channel_guide = 4;

            $well_channel_recommend = param('well_channel_recommend', 0);
            !$well_channel_recommend AND $well_channel_recommend = 7;

        } else {
            // 列表需要显示数据
            $well_channel_slides = 0;
            $well_channel_headline = 0;
            $well_channel_guide = 0;
            $well_channel_recommend = 0;
            $well_comment = param('well_comment', 0);
            $well_display = param('well_display', 0);

            $well_news = param('well_news', 0);
            $well_display AND !$well_news AND $well_news = 10;

            $well_channel_display = param('well_channel_display', 0);
            $well_headlines = param('well_headlines', 0);
            $well_display AND !$well_headlines AND $well_headlines = 2;

            $well_channel_news = param('well_channel_news', 0);
            $well_channel_display AND !$well_channel_news AND $well_channel_news = 10;

            $well_channel_headlines = param('well_channel_headlines', 0);
            $well_channel_display AND !$well_channel_headlines AND $well_channel_headlines = 2;

            $well_list_news = param('well_list_news', 10);
            $well_list_headlines = param('well_list_headlines', 10);
            $well_list_recommends = param('well_list_recommends', 10);
            $well_pagesize = param('well_pagesize', 20);
        }

        // 网站 & 自建模板
        if ($well_type == 1 && $well_tpl == 1) {
            // 0列表 1频道

            // 用户自行开发的模板上传路径
            $path = APP_PATH . './plugin/well_cms/view/template/';

            // hook well_admin_forum_update_post_template.php

            // 列表&频道模板
            if ($well_forum_type == 1 || $well_forum_type == 0) {
                if (!$well_tpl_class) message('well_tpl_class', lang('well_template_name_error'));
                // 为了安全计算长度后 截取后5位 如果不是htm或html后缀不通过
                $strlen = xn_strlen($well_tpl_class);
                $cate_tplstr = xn_substr($well_tpl_class, $strlen - 5, 5);
                $str_cate_tpl = strstr($cate_tplstr, '.htm');
                if ($str_cate_tpl === FALSE) message('well_tpl_class', lang('well_template_name_error'));
                // 检查文件是否存在
                if (!file_exists($path . $well_tpl_class)) message('well_tpl_class', lang('well_no_templatetpl_file'));
            }

            // 内容页模板
            if ($well_forum_type == 0) {
                if (!$well_tpl_show) message('well_tpl_show', lang('well_template_error'));
                // 为了安全计算长度后 截取后5位 如果不是htm或html后缀不通过
                $strlen = xn_strlen($well_tpl_show);
                $show_tplstr = xn_substr($well_tpl_show, $strlen - 5, 5);
                $str_show_tpl = strstr($show_tplstr, '.htm');
                if ($str_show_tpl === FALSE) message('well_tpl_show', lang('well_template_error'));
                if (!file_exists($path . $well_tpl_show)) message('well_tpl_show', lang('well_no_templatetpl_file'));
            }
        }

        // hook well_content_setting_column_post_arr_before.php

        $arr['well_type'] = $well_type;
        $arr['well_nav_display'] = $well_nav_display;
        $arr['well_model'] = $well_model;
        $arr['well_forum_type'] = $well_forum_type;
        $arr['well_fup'] = $well_fup;
        $arr['well_tpl'] = $well_tpl;
        $arr['well_tpl_class'] = $well_tpl_class;
        $arr['well_tpl_show'] = $well_tpl_show;
        $arr['well_comment'] = $well_comment;
        $arr['well_display'] = $well_display;
        $arr['well_news'] = $well_news;
        $arr['well_headlines'] = $well_headlines;
        $arr['well_channel_display'] = $well_channel_display;
        $arr['well_channel_news'] = $well_channel_news;
        $arr['well_channel_headlines'] = $well_channel_headlines;
        $arr['well_channel_slides'] = $well_channel_slides;
        $arr['well_channel_headline'] = $well_channel_headline;
        $arr['well_channel_guide'] = $well_channel_guide;
        $arr['well_channel_recommend'] = $well_channel_recommend;
        $arr['well_list_news'] = $well_list_news;
        $arr['well_list_headlines'] = $well_list_headlines;
        $arr['well_list_recommends'] = $well_list_recommends;
        $arr['well_pagesize'] = $well_pagesize;

        $_fid AND forum_update($_fid, $arr);
        !$_fid AND $_fid = forum_create($arr);;

        if ($accesson) {
            $allowread = param('allowread', array(0));
            $allowthread = param('allowthread', array(0));
            $allowpost = param('allowpost', array(0));
            $allowattach = param('allowattach', array(0));
            $allowdown = param('allowdown', array(0));
            foreach ($grouplist as $_gid => $v) {
                $access = array(
                    'allowread' => array_value($allowread, $_gid, 0),
                    'allowthread' => array_value($allowthread, $_gid, 0),
                    'allowpost' => array_value($allowpost, $_gid, 0),
                    'allowattach' => array_value($allowattach, $_gid, 0),
                    'allowdown' => array_value($allowdown, $_gid, 0),
                );
                forum_access_replace($_fid, $_gid, $access);
            }
        } else {
            forum_access_delete_by_fid($_fid);
        }

        // hook well_content_setting_column_post_end.php

        forum_list_cache_delete();
        well_cms_delete_cache();

        message(0, lang('well_operate_success'));
    }

}

function well_user_names_to_ids($names, $sep = ',')
{
    $namearr = explode($sep, $names);
    $r = array();
    foreach ($namearr as $name) {
        $user = user_read_by_username($name);
        if (empty($user)) continue;
        $r[] = $user ? $user['uid'] : 0;
    }
    return implode($sep, $r);
}

function well_user_ids_to_names($ids, $sep = ',')
{
    $idarr = explode($sep, $ids);
    $r = array();
    foreach ($idarr as $id) {
        $user = user_read($id);
        if (empty($user)) continue;
        $r[] = $user ? $user['username'] : '';
    }
    return implode($sep, $r);
}

// hook well_content_setting_column_end.php

?>