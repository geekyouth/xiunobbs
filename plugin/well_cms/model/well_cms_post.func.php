<?php
// hook model_well_cms_post_start.php

// 编辑内容
function well_cms_post_update($post = array(), $arr = array())
{
    global $gid;

    if (empty($post)) return FALSE;

    $tid = intval($post['tid']);
    $pid = intval($post['pid']);

    // hook model_well_cms_post_update_start.php

    // 写入时格式化
    post_message_fmt($arr, $gid);

    // hook model_well_cms_post_create_before.php

    // 更新
    $r = post__update($pid, $arr);
    $r === FALSE AND message(-1, lang('update_post_failed'));

    // hook model_well_cms_post_update_end.php
    return $r;
}

// hook model_well_cms_post_end.php

?>