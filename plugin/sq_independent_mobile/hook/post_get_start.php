if(IS_MOBILE) {
    $header['title'] = lang('post_create');
    $header['mobile_title'] = lang('post_create');
    $header['mobile_link'] = url("thread-$tid");

    include _include(APP_PATH . SQ_MOBILE_PATH . 'view/htm/post.htm');
    return;
}