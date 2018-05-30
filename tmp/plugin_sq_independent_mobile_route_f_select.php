<?php
    $condition = param(1);

    $fid = param(2, 0); // 根据板块id获得tag

    if($condition == 'f') { // 选择板块的列表
        $forumList = get_forum_list();
        $noBottomNav = true; // 不显示底部菜单

        if($method == 'GET') {
            include _include('./' . SQ_MOBILE_PATH . '/view/htm/forum_select.htm');
        } else if($method == 'POST') {
            echo json_encode(['code' => 0, 'data' => $forumList]);
            die;
        }
    } else if($condition == 't') { // 标签列表
        $tagList = get_tag_by_fid($fid);
        $noBottomNav = true; // 不显示底部菜单
        if($method == 'GET') {
        include _include('./' . SQ_MOBILE_PATH . '/view/htm/forum_select.htm');
        } else if($method == 'POST') {
            echo json_encode(['code' => 0, 'data' => $tagList]);
            die;
        }
    } else if($condition == 'all') {
        $forumAndTag = get_forum_and_tag();

        include _include('./'. SQ_MOBILE_PATH . '/view/htm/forum_tag_list.htm');
    }

    
    