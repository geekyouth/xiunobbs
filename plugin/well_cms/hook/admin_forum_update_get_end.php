<?php exit;
// 插入更新版块代码
// 论坛频道
$well_bbslist = well_get_bbs_channellist($forumlist);

// CMS频道
$well_cmslist = well_get_cms_channellist($forumlist);

// 0论坛 1网站
$arrlist = array();
if ($_forum['well_type'] == 1) {
    $well_arrlist = $well_cmslist;
} else {
    $well_arrlist = $well_bbslist;
}

$well_channel_name = '顶级栏目(非频道)';
foreach ($well_arrlist as $v) {
    if ($_forum['well_fup'] == $v['fid']) {
        $well_channel_name = $v['name'];
    }
}

// 0:BBS 1:CMS
if ($_forum['threads'] || $_forum['well_son']) {

    $input['well_type'] = form_hidden('well_type', $_forum['well_type']);

    // bbs
    if ($_forum['well_type'] == 0) {
        $well_typename = lang('well_forum_bbs');
        $input['well_fup'] = form_hidden('well_fup', $_forum['well_fup']);
    }

    // cms
    if ($_forum['well_type'] == 1) {
        $well_typename = lang('well_forum_cms');
        $input['well_fup'] = form_hidden('_well_fup', $_forum['well_fup']);
    }

    $well_typename .= ' ' . lang('well_forum_no_update');

} else {
    // 没有内容 没有子栏目可以修改
    $input['well_type'] = form_radio('well_type', lang('well_web_type_radio'), $_forum['well_type']);
    $well_typename = '';
}

// 栏目是否显示在导航
$input['well_nav_display'] = form_radio_yes_no('well_nav_display', $_forum['well_nav_display']);

// 0列表 1频道
//$type_name = '';
$well_forum_name = '';
if ($_forum['well_forum_type'] == 1) {
    // 频道下没有子栏目可以修改
    if ($_forum['well_son'] == 0) {
        $input['well_forum_type'] = form_radio('well_forum_type', lang('well_forum_type_radio'), $_forum['well_forum_type']);
    } else {
        $input['well_forum_type'] = form_hidden('well_forum_type', $_forum['well_forum_type']);
        $well_forum_name = lang('well_channel_no_update');
    }
} else {
    // 列表下没有内容可以修改
    if ($_forum['threads'] == 0) {
        $input['well_forum_type'] = form_radio('well_forum_type', lang('well_forum_type_radio'), $_forum['well_forum_type']);
    } else {
        $input['well_forum_type'] = form_hidden('well_forum_type', $_forum['well_forum_type']);
        $well_forum_name = lang('well_list_no_update');
    }
}

$input['well_tpl_class'] = form_text('well_tpl_class', $_forum['well_tpl_class']);

$input['well_tpl_show'] = form_text('well_tpl_show', $_forum['well_tpl_show']);

// 首页是否显示内容 1显示
$input['well_display'] = form_radio_yes_no('well_display', $_forum['well_display']);
// 频道是否显示内容 1显示
$input['well_channel_display'] = form_radio_yes_no('well_channel_display', $_forum['well_channel_display']);

// 设置为频道 轮播 推荐 导读 头条显示
// 轮播
$input['well_channel_slides'] = form_text('well_channel_slides', $_forum['well_channel_slides']);
// 头条
$input['well_channel_headline'] = form_text('well_channel_headline', $_forum['well_channel_headline']);
// 导读
$input['well_channel_guide'] = form_text('well_channel_guide', $_forum['well_channel_guide']);
// 推荐
$input['well_channel_recommend'] = form_text('well_channel_recommend', $_forum['well_channel_recommend']);

$input['well_tpl'] = form_radio('well_tpl', lang('well_tpl_template_radio'), $_forum['well_tpl']);

// 是开启评论
$input['well_comment'] = form_radio_yes_no('well_comment', $_forum['well_comment']);

// 版块模型 加载不同模型
$well_model_arr = array();
$well_model_arr[] = lang('well_model_radio_news');

// hook well_admin_forum_update_well_model_arr.php

$input['well_model'] = form_radio('well_model', $well_model_arr, $_forum['well_model']);

$input['well_news'] = form_text('well_news', $_forum['well_news']);

$input['well_headlines'] = form_text('well_headlines', $_forum['well_headlines']);

$input['well_channel_news'] = form_text('well_channel_news', $_forum['well_channel_news']);

$input['well_channel_headlines'] = form_text('well_channel_headlines', $_forum['well_channel_headlines']);

$input['well_list_news'] = form_text('well_list_news', $_forum['well_list_news']);

$input['well_list_headlines'] = form_text('well_list_headlines', $_forum['well_list_headlines']);

$input['well_list_recommends'] = form_text('well_list_recommends', $_forum['well_list_recommends']);

?>