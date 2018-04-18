<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$gid != 1 AND message(-1, lang('insufficient_visit_forum_privilege'));

$setting_route = param(3, 'base');

// hook well_content_setting_system_start.php

if ($setting_route == 'base') {

    $well_conf = $_SERVER['well_conf'];

    // hook well_content_setting_system_get_start.php

    if ($method == 'GET') {

        $header['title'] = lang('well_setting_system');
        $header['mobile_title'] = lang('well_setting_system');
        $header['mobile_link'] = url('content-setting-system-base');

        // hook well_cms_admin_setting_get_start.php
        $well_conf = $well_conf['setting'];
        $input = array();
        $admin_well_web_radio = array(lang('well_web_radio'), lang('well_web_flat'));
        // hook well_content_setting_system_well_web_radio.php
        $input['web_type'] = form_radio('web_type', $admin_well_web_radio, $well_conf['web_type']);
        $input['mobile'] = form_radio('mobile', lang('well_mobile'), $well_conf['mobile']);
        $input['register'] = form_radio_yes_no('register', $well_conf['register']);
        $input['single_login'] = form_radio_yes_no('single_login', $well_conf['single_login']);

        $well_conf = $_SERVER['well_conf']['mainpic_size'];

        $input['width'] = form_text('width', $well_conf['width']);
        $input['height'] = form_text('height', $well_conf['height']);

        // 首页显示
        $well_conf = $_SERVER['well_conf']['setting']['index'];
        // 轮播
        $input['slides'] = form_text('slides', $well_conf['slides']);
        // 头条
        $input['headline'] = form_text('headline', $well_conf['headline']);
        // 导读
        $input['guide'] = form_text('guide', $well_conf['guide']);
        // 推荐
        $input['recommend'] = form_text('recommend', $well_conf['recommend']);
        // 推荐标签数量
        $input['tag'] = form_text('tag', $well_conf['tag']);
        // 推荐栏目数量
        //$input['menu'] = form_text('menu', $well_conf['menu']);

        // hook well_content_setting_system_get_before.php

        include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_system_base.htm');

    } elseif ($method == 'POST') {

        $web_type = param('web_type', 0);
        $mobile = param('mobile', 0);
        $register = param('register', 0);
        $single_login = param('single_login', 0);

        $width = param('width', 0);
        $height = param('height', 0);

        $slides = param('slides', 0);
        $headline = param('headline', 0);
        $guide = param('guide', 0);
        $recommend = param('recommend', 0);
        $tag = param('tag', 0);
        //$menu = param('menu');

        // hook well_content_setting_system_post_start.php

        $replace = array();
        $replace['web_type'] = $web_type;
        $replace['mobile'] = $mobile;
        $replace['register'] = $register;
        $replace['single_login'] = $single_login;

        $replace['index']['slides'] = $slides;
        $replace['index']['headline'] = $headline;
        $replace['index']['guide'] = $guide;
        $replace['index']['recommend'] = $recommend;
        $replace['index']['tag'] = $tag;
        //$replace['index']['menu'] = $menu;

        // hook well_content_setting_system_post_before.php

        $well_conf['setting'] = $replace;
        $well_conf['mainpic_size']['width'] = $width;
        $well_conf['mainpic_size']['height'] = $height;

        // hook well_content_setting_system_post_center.php

        well_cms_conf_write($well_conf);

        // hook well_content_setting_system_post_end.php

        message(0, lang('modify_successfully'));
    }

}

// hook well_content_setting_system_end.php

?>