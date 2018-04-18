if($post['images'] > 0) {
    list($attachlist, $imagelist, $filelist) = attach_find_by_pid($post['pid']);
    $post['imagelist'] = $imagelist;
}

