<?php

// hook model_well_cms_admin_start.php

function well_cms_conf()
{
    // hook model_well_cms_conf_start.php
    //$_SERVER['well_conf'] = (@include APP_PATH . './plugin/well_cms/conf/conf.php');
    $_SERVER['well_conf'] = setting_get('well_conf');
    // hook model_well_cms_conf_end.php
}

// 增删改 前台和后台导航缓存
function well_cms_conf_write($arr = array())
{
    $conf = $_SERVER['conf'];
    // hook model_well_cms_conf_write_start.php
    // 写入文件备份
    //file_replace_var(APP_PATH . './plugin/well_cms/conf/conf.php', $arr);
    setting_set('well_conf', $arr);
    // hook model_well_cms_conf_write_end.php
}

// hook model_well_cms_admin_end.php

?>