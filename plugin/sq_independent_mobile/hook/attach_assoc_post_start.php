// 将两个图片数组合并
$sess_tmp_files_sq = $_SESSION['tmp_files_sq']; // 获得session里面定义的文件
$sess_tmp_files = array_merge($sess_tmp_files, $sess_tmp_files_sq);

$attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym'); // 获得保存路径的规则
$day = date($attach_dir_save_rule, $time); // 保存的日期
$thumbImgPath = APP_PATH . 'upload/attach/thumb/' . $day; // 保存路径

foreach($sess_tmp_files as $_file) { // 循环生成缩略图
	$filename = file_name($_file['url']);
	$fileType = get_file_type($filename);
	
	$filename = str_replace(".", ".thumb.", $filename);

	if(file_exists($_file['url']) && $fileType == 'image'){ // 只有文件存在，并且为图片格式才生成缩略图
		get_compress_image($_file['url'], $thumbImgPath, $filename);
	}
}