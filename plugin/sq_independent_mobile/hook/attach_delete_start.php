<?php exit;
    $isSq = param(3);
    if($isSq) {
        if(substr($aid, 0, 1) == '_'){
            $key = intval(substr($aid, 1));
            $tmp_files = _SESSION('tmp_files_sq');
            !isset($tmp_files[$key]) AND message(-1, lang('item_not_exists', array('item'=>$key)));
            $attach = $tmp_files[$key];
            
            !is_file($attach['path']) AND message(-1, lang('file_not_exists'));
            unlink($attach['path']);
            unset($_SESSION['tmp_files_sq'][$key]);
        } else {
            $aid = intval($aid);
            $attach = attach_read($aid);
            empty($attach) AND message(-1, lang('attach_not_exists'));
            
            $thread = thread_read($attach['tid']);
            empty($thread) AND message(-1, lang('thread_not_exists'));
            $fid = $thread['fid'];
            
            $allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
            $attach['uid'] != $uid AND !$allowdelete AND message(0, lang('insufficient_privilege'));
            
            $r = attach_delete($aid);
            $r ===  FALSE AND message(-1, lang('delete_failed'));
        }
        message(0, 'delete_successfully');
    }