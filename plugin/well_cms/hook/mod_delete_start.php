<?php exit;
// 删除wellcms数据
foreach ($threadlist as $thread) {
    $fid = $thread['fid'];
    $tid = $thread['tid'];
    if (forum_access_mod($fid, $gid, 'allowmove') && !empty($forumlist[$fid]['well_type'])) {

        $thread_read = well_cms_thread_read($tid);

        $author_id = $thread_read['author_id'];
        well_cms_author_update($author_id, array('count-' => 1));
        well_cms_author_thread_delete_tid($author_id, $tid);

        $source_id = $thread_read['source_id'];
        well_cms_source_update($source_id, array('count-' => 1));
        well_cms_source_thread_delete_tid($source_id, $tid);

        well_cms_thread_delete($fid, $tid);
    }
}
?>