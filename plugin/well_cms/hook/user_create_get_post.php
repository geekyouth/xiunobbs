<?php exit;
// 关闭用户注册
$register = $_SERVER['well_conf']['setting']['register'];
if ($register == 0) {
    message(-1, jump(lang('well_register_close'), './', 2));
}
?>