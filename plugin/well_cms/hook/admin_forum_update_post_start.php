<?php exit;
// 接收编辑版块数据
$well_type = param('well_type', 0);
$well_nav_display = param('well_nav_display', 0);
$well_model = param('well_model', 0);
$well_forum_type = param('well_forum_type', 0);
if ($well_type == 1) {
    $well_fup = param('_well_fup', 0);//CMS
} else {
    $well_fup = param('well_fup', 0);
}

$well_tpl = param('well_tpl', '', FALSE);
$well_tpl_class = param('well_tpl_class', '', FALSE);
$well_tpl_show = param('well_tpl_show', '', FALSE);

if ($_forum['fid'] == $well_fup) message(-1, lang('well_forum_channel_error'));
//if ($well_type == 2 && $well_fup) message(-1, lang('well_channel_create_sub_column'));
// 修改前配置有pid
if ($_forum['well_fup']) {
    if ($_forum['well_fup'] != $well_fup) {
        // 旧频道-1
        forum_update($_forum['well_fup'], array('well_son-' => 1));
        // 新频道+1
        $well_fup AND forum_update($well_fup, array('well_son+' => 1));
    }
} else {// 没有pid增加
    forum_update($well_fup, array('well_son+' => 1));
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

?>