// 生成缩略图
include _include(APP_PATH . SQ_MOBILE_PATH . '/model/plugin.func.php');

$attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym'); // 获得保存路径的规则
$day = date($attach_dir_save_rule, $time); // 保存的日期
$thumbImgPath = APP_PATH . 'upload/attach/thumb/' . $day; // 保存路径

foreach($sess_tmp_files as $_file) { // 循环生成缩略图
	$filename = file_name($_file['url']);
	$filename = str_replace(".", ".thumb.", $filename);
	
	get_compress_image($_file['url'], $thumbImgPath, $filename);
}