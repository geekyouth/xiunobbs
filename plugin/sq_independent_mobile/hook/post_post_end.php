<?php exit;

    foreach($postlist as &$_post) { // 返回的postlist中附加该post的图片附件信息
        $imgs = get_imgs_by_postid($_post['pid']);
        $_post['sq_imgs'] = $imgs;
    }

    $return_html = param('return_html', 0);
    if($return_html) {
        $filelist = array();
        ob_start();
        include _include(APP_PATH . SQ_MOBILE_PATH . 'view/htm/thread_post_list.inc.htm');
        $s = ob_get_clean();
                    
        message(0, $s);
    } else {
        message(0, lang('create_post_sucessfully'));
    }