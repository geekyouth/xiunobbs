<?php exit;
// CMS内容页
if ($forum['well_type'] == 1) {
    // 版块URL
    $forum_url = well_nav_url_format($forum);

    $thread_url = well_url_alias($forum, $thread['tid']);

    well_cms_well_thread_one_format($thread);

    // hook well_thread_one_format_before.php

    $well_thread_list = wel_cms_thread_info_list($fid, $forum['well_list_headlines'], $forum['well_list_recommends'], $forum['well_list_news']);
    // 头条
    $headlinelist = array_value($well_thread_list, 'headline');
    // 推荐
    $recommendlist = array_value($well_thread_list, 'recommend');

    $header['keywords'] = well_thread_tag_to_str($thread['tag']);
    $header['description'] = $thread['brief'];
    $header['mobile_link'] = $forum_url;

    // hook well_thread_recommend_before.php

    if ($ajax) {
        $thread = thread_safe_info($thread);
        foreach ($postlist as &$post) $post = post_safe_info($post);
        message(0, array('thread' => $thread, 'postlist' => $postlist));
    } else {
        include _include(well_cms_template_htm(3, $forum));
    }
    exit();
}
?>