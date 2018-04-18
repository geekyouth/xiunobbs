<?php exit;
$menu['setting']['tab']['cms'] = array('url' => url("setting-cms"), 'text' => lang('well_cms_setting'));

if ($action == 'cms') {

    well_cms_conf();
    $well_conf = $_SERVER['well_conf'];
    // hook well_cms_admin_setting_start.php

    if ($method == 'GET') {

        $header['title'] = lang('well_cms_setting');
        $header['mobile_title'] = lang('well_cms_setting');
        $well_conf = $well_conf['setting'];
        // hook well_cms_admin_setting_get_start.php

        $input = array();
        $admin_well_web_radio = array(lang('well_web_radio'));
        // hook well_admin_well_web_radio.php
        $input['web_type'] = form_radio('web_type', $admin_well_web_radio, $well_conf['web_type']);
        $input['mobile'] = form_radio('mobile', lang('well_mobile'), $well_conf['mobile']);
        $input['register'] = form_radio_yes_no('register', $well_conf['register']);

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

        // hook well_cms_admin_setting_get_end.php

        include _include(APP_PATH . './plugin/well_cms/view/htm/admin_cms_setting.htm');

    } elseif ($method == 'POST') {


        $web_type = intval(param('web_type', 0));
        $mobile = intval(param('mobile', 0));
        $register = intval(param('register', 0));

        $width = intval(param('width', 0));
        $height = intval(param('height', 0));

        $slides = intval(param('slides', 0));
        $headline = intval(param('headline', 0));
        $guide = intval(param('guide', 0));
        $recommend = intval(param('recommend', 0));
        $tag = intval(param('tag', 0));
        //$menu = intval(param('menu'));

        // hook well_cms_admin_setting_post_start.php

        $replace = array();
        $replace['web_type'] = $web_type;
        $replace['mobile'] = $mobile;
        $replace['register'] = $register;

        $replace['index']['slides'] = $slides;
        $replace['index']['headline'] = $headline;
        $replace['index']['guide'] = $guide;
        $replace['index']['recommend'] = $recommend;
        $replace['index']['tag'] = $tag;
        //$replace['index']['menu'] = $menu;

        // hook well_cms_admin_setting_post_before.php

        $well_conf['setting'] = $replace;
        $well_conf['mainpic_size']['width'] = $width;
        $well_conf['mainpic_size']['height'] = $height;

        // hook well_cms_admin_setting_post_center.php

        well_cms_conf_write($well_conf);

        // hook well_cms_admin_setting_post_end.php

        message(0, lang('modify_successfully'));
    }
}

?>