<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$_conf = $_SERVER['well_conf'];

// hook well_content_setting_filter_start.php

if ($method == 'GET') {

    $header['title'] = lang('well_content_filter');
    $header['mobile_title'] = lang('well_content_filter');
    $header['mobile_link'] = url('content-setting-filter');

    // hook well_content_setting_filter_get_start.php
    $well_conf = $_conf['filter'];
    $action = url('content-setting-filter');
    $_user = $well_conf['username'];

    $input = array();
    $input['username_enable'] = form_radio_yes_no('username_enable', $_user['enable']);
    $input['username_keyword'] = form_textarea('username_keyword', well_array_to_str($_user['keyword']), '100%', '200px');

    $_content = $well_conf['content'];
    $input['content_enable'] = form_radio_yes_no('content_enable', $_content['enable']);
    $input['content_keyword'] = form_textarea('content_keyword', well_array_to_str($_content['keyword']), '100%', '200px');

    // hook well_content_setting_filter_get_end.php

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_filter.htm');

} elseif ($method == 'POST') {

    // hook well_content_setting_filter_post_start.php

    $username_enable = param('username_enable', 0);
    $well_conf['username']['enable'] = $username_enable;

    $username_keyword = param('username_keyword', '', FALSE);
    $keyword = well_str_to_array($username_keyword, '|');
    $well_conf['username']['keyword'] = $keyword;

    $content_enable = param('content_enable', 0);
    $well_conf['content']['enable'] = $content_enable;

    $content_keyword = param('content_keyword', '', FALSE);
    $keyword = well_str_to_array($content_keyword, '|');
    $well_conf['content']['keyword'] = $keyword;

    // hook well_content_setting_filter_post_html.php

    $_conf['filter'] = $well_conf;
    setting_set('well_conf', $_conf);

    // hook well_content_setting_filter_post_end.php

    message(0, lang('well_operate_success'));
}

// hook well_content_setting_column_end.php

?>