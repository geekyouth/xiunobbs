<?php exit;
// 过滤用户名中的关键词
if (well_filter_keyword($username, 'username', $error) !== FALSE) {
    $err = lang('well_username_contain_keyword') . $error;
    return FALSE;
}
?>