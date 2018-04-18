<?php

// 验证token 单点登录
function well_check_token($user)
{
    // single login
    $single_login = array_value($_SERVER['well_conf']['setting'], 'single_login');
    if (!$single_login) return;
    $token = _COOKIE('bbs_token');
    $well_token = array_value($user, 'well_token');
    if ($well_token != $token) {
        $_SESSION['uid'] = 0;
        user_token_clear();
        message(0, jump(lang('well_user_logout_tips'), './', 3));
    }
}

?>