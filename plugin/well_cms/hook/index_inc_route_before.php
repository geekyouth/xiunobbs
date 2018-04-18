<?php exit;
// 配置数据
well_cms_conf();
// 验证token 单点登录
well_check_token($user);
// 导航栏
$well_nav = well_cms_web_nav($forumlist);
// modellog调用栏目
$forumarr = well_get_forumarr($forumlist);
?>