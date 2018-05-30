else if($action == 'getsess') {

    $filelistSq = $_SESSION['tmp_files_sq'];

    $html = post_img_list_html($filelistSq);
    echo json_encode(['code' => 0, 'data' => $html]);
    die;
}