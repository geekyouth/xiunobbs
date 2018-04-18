<?php exit;
// 主题移动 删除主题属性 更新主题附表 更新版块属性统计数
foreach ($threadlist as $thread) {
    $fid = $thread['fid'];
    $tid = $thread['tid'];
    if (forum_access_mod($fid, $gid, 'allowmove') && !empty($forumlist[$fid]['well_type'])) {
        well_cms_thread_move($fid, $tid);
    }
}
?>