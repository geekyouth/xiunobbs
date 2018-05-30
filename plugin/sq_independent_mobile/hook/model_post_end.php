<?php exit;
/** 发帖里面的图片添加内容 */
function post_img_list_html($filelistSq) {
    if(empty($filelistSq)) return '';

    $filelistSq = arrlist_multisort($filelistSq, 'aid');

    $deleteIcon = '<span class="img-delete-icon" onclick="sqDeleteImg(this);"></span>';
    $html = '';
    foreach ($filelistSq as &$attach) {
        $html .= '<div class="sq-img-div" data-id="' . $attach['aid'] . '" style="background-image: url(' . $attach['url'] . ')">' . $deleteIcon . '</div>';
    }
    return $html;
}

/** 根据pid获得当前pid的图片 */
function get_imgs_by_postid($pid) {
    $imgs = db_find('attach', ['pid' => $pid], ['aid' => 1], 1, 5, '', ['aid', 'filename']);
    return $imgs;
}