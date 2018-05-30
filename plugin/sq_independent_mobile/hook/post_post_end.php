<?php exit;

    foreach($postlist as &$_post) { // 返回的postlist中附加该post的图片附件信息
        $imgs = get_imgs_by_postid($_post['pid']);
        $_post['sq_imgs'] = $imgs;
    }