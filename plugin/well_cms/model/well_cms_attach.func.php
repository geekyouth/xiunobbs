<?php

// hook model_well_cms_attach_start.php

// 主图预览关联session临时文件 返回数组 处理well_thread表再处理post
function well_cms_attach_assoc($pid, $tid)
{
    global $uid;

    $time = $_SERVER['time'];
    $conf = $_SERVER['conf'];

    $attach_dir_save_rule = array_value($conf, 'attach_dir_save_rule', 'Ym');

    $pictures = array();
    $tmp_files = _SESSION('tmp_mainpic');
    $return = array();
    if (!empty($tmp_files)) {
        foreach ($tmp_files as $key => $file) {
            // 将文件移动到 upload/attach 目录
            $filename = file_name($file['url']);

            $day = date($attach_dir_save_rule, $time);
            $path = $conf['upload_path'] . 'attach/' . $day;
            $url = $conf['upload_url'] . 'attach/' . $day;
            !is_dir($path) AND mkdir($path, 0777, TRUE);

            $destfile = $path . '/' . $filename;
            $picture = $url . '/' . $filename;

            $r = xn_copy($file['path'], $destfile);
            !$r AND xn_log("xn_copy($file[path]), $destfile) failed, pid:$pid, tid:$tid", 'php_error');
            if (is_file($destfile) && filesize($destfile) == filesize($file['path'])) {
                @unlink($file['path']);
            }

            $arr = array(
                'tid' => $tid,
                'pid' => $pid,
                'uid' => $uid,
                'filesize' => $file['filesize'],
                'width' => $file['width'],
                'height' => $file['height'],
                'filename' => "$day/$filename",
                'orgfilename' => $file['orgfilename'],
                'filetype' => $file['filetype'],
                'create_date' => $time,
                'comment' => '',
                'downloads' => 0,
                'isimage' => $file['isimage']
            );

            // 插入后，进行关联
            $aid = attach_create($arr);
            $return[$key] = array('aid' => $aid, 'picture' => $picture);
        }
    }

    // 清空 session
    $_SESSION['tmp_mainpic'] = array();

    return $return;
}

// hook model_well_cms_attach_end.php

?>