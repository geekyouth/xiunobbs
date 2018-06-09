<?php exit;

// Log::write('image' . json_encode($imagelist), 'log');
// Log::write('files:' . json_encode($filelist), 'log');

// 和post关联成功后删除session里面的文件信息
$_SESSION['tmp_files_sq'] = array(); // 清空session