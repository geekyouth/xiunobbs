else if($action == 'getsess') { // 获得当前sq定义的tmp_files_sq，如果session里面有该下标，返回一个组装好的html

    $filelistSq = $_SESSION['tmp_files_sq'];

    $html = post_img_list_html($filelistSq);
    echo json_encode(['code' => 0, 'data' => $html]);
    die;
}