<?php exit;

    foreach($postlist as &$_post) {
        $imgs = get_imgs_by_postid($_post['pid']);
        $_post['sq_imgs'] = $imgs;
    }