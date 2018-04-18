<?php

// hook model_well_cms_thread_start.php

function well_cms__thread__create($arr)
{
    // hook model_well_cms__thread__create_start.php
    $r = db_insert('well_thread', $arr);
    // hook model_well_cms__thread__create_end.php
    return $r;
}

function well_cms_thread_update($tid, $arr)
{
    if (empty($arr)) return TRUE;
    // hook model_well_cms_thread_update_start.php
    $r = db_update('well_thread', array('tid' => $tid), $arr);
    // hook model_well_cms_thread_update_end.php
    return $r;
}

function well_cms_thread_read($tid)
{
    // hook model_well_cms_thread_read_start.php
    $thread = db_find_one('well_thread', array('tid' => $tid));
    // hook model_well_cms_thread_read_end.php
    return $thread;
}

function well_cms__thread__delete($tid)
{
    // hook model_well_cms__thread__delete_start.php
    $r = db_delete('well_thread', array('tid' => $tid));
    // hook model_well_cms__thread__delete_end.php
    return $r;
}

function well_cms__thread__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms__thread__find_start.php
    //$threadlist = db_find('well_thread', $cond, $orderby, $page, $pagesize, 'tid');
    $threadlist = db_find('well_thread', $cond, $orderby, $page, $pagesize, 'tid');

    // hook model_well_cms__thread__find_end.php
    return $threadlist;
}

function well_cms_well_thread_find($tid, $page, $pagesize)
{
    // hook model_well_cms_well_thread_find_start.php

    $threadlist = well_cms__thread__find(array('tid' => $tid), array('tid' => -1), $page, $pagesize);

    // hook model_well_cms_well_thread_find_end.php

    return $threadlist;
}

//--------------------------强相关数据--------------------------

// 查询主题表并格式化wellcms附表
function well_cms_thread_find($tidarr, $orderby, $page, $pagesize)
{
    // hook model_well_cms_thread_find_start.php

    $threadlist = db_find('thread', array('tid' => $tidarr), $orderby, $page, $pagesize, 'tid');
    //$threadlist = thread_find(array('tid' => $tidarr), $orderby, $page, $pagesize);

    // hook model_well_cms_thread_find_before.php

    // 主题 URL 格式化
    well_thread_url_format($threadlist);

    // hook model_well_cms_thread_url_format_before.php

    well_cms_well_thread_format($threadlist, $pagesize, $tidarr);

    // hook model_well_cms_thread_find_end.php

    return $threadlist;
}

// 此处可判断是否开启了附表 如果不需要显示主图 简介 推荐位
// 格式化wellcms主题附表数据，与主题表数据合并 $tidarr = array(1,2,3,4)
function well_cms_well_thread_format(&$threadlist, $pagesize, $tidarr)
{
    // hook model_well_cms_well_thread_format_start.php

    $well_thread = well_cms_well_thread_find($tidarr, $page = 1, $pagesize);

    // hook model_well_cms_well_thread_format_before.php

    foreach ($threadlist as &$_thread) {
        $_thread['url'] = well_url_format($_thread['fid'], $_thread['tid']);
        $_thread['subject'] = htmlspecialchars_decode($_thread['subject']);
        $_well_thread = array_value($well_thread, $_thread['tid']);
        $tag = array_value($_well_thread, 'tag');
        $_thread['tag'] = $tag ? xn_json_decode($tag) : '';

        $flag = array_value($_well_thread, 'flag');
        $_thread['flag'] = well_cms_thread_flag_array($flag);

        $_thread['mainpic'] = well_mainpic($_well_thread);
        $_thread['brief'] = array_value($_well_thread, 'brief');
        $_thread['brief'] = $_thread['brief'] ? htmlspecialchars_decode($_thread['brief']) : '';

        // hook model_well_cms_well_thread_format_after.php
    }

    // hook model_well_cms_well_thread_format_end.php
}

// 格式化wellcms主题附表数据，与主题表数据合并 $tidarr = array(1,2,3,4)
function well_cms_well_thread_one_format(&$thread)
{
    if (empty($thread)) return;

    // hook model_well_cms_well_thread_one_format_start.php

    $_thread = well_cms_thread_read($thread['tid']);

    // hook model_well_cms_well_thread_one_format_before.php

    $thread['tag'] = xn_json_decode($_thread['tag']);
    $thread['flag'] = well_cms_thread_flag_array($_thread['flag']);
    $thread['brief'] = htmlspecialchars_decode($_thread['brief']);
    $thread['mainpic'] = well_mainpic($_thread);

    $thread['author_name'] = '';
    if ($_thread['author_id']) {
        $author_read = well_cms_author_read($_thread['author_id']);
        $thread['author_name'] = array_value($author_read, 'name');
    }

    $thread['source_name'] = '';
    if ($_thread['source_id']) {
        $source_read = well_cms_source_read($_thread['source_id']);
        $thread['source_name'] = array_value($source_read, 'link') ? '<a href=' . $source_read['link'] . ' target="_blank">' . array_value($source_read, 'name') . '</a>' : array_value($source_read, 'name');
    }

    // hook model_well_cms_well_thread_one_format_end.php
}

// 格式化主题URL
function well_thread_url_format(&$threadlist)
{
    // hook model_well_thread_url_format_start.php
    foreach ($threadlist as &$_thread) {
        $_thread['url'] = well_url_format($_thread['fid'], $_thread['tid']);
        // hook model_well_thread_url_format_after.php
    }
    // hook model_well_thread_url_format_end.php
}

// 一 格式化 URL 根据fid查询版块是否配置别名 well_alias
function well_url_format($fid, $tid)
{
    // hook model_well_url_format_start.php
    $url = url('thread-' . $tid);
    // hook model_well_url_format_end.php
    return $url;
}

// 二 格式化 URL 可根据别名 well_alias 直接返回
function well_url_alias($forum, $tid)
{
    // hook model_well_url_alias_start.php
    $url = url('thread-' . $tid);
    // hook model_well_url_alias_end.php
    return $url;
}

// 还原html字符
function well_cms_thread_decode_format(&$threadlist)
{
    if (empty($threadlist)) return;

    // hook model_well_cms_thread_decode_format_start.php

    foreach ($threadlist as &$_thread) {
        $_thread['subject'] = htmlspecialchars_decode($_thread['subject']);
        $_thread['brief'] = htmlspecialchars_decode($_thread['brief']);
        // hook model_well_cms_thread_decode_format_before.php
    }

    // hook model_well_cms_thread_decode_format_end.php
}

// 删除wellcms数据
function well_cms_thread_delete($fid, $tid)
{
    // hook model_well_cms_thread_delete_start.php

    $well_thread = well_cms_thread_read($tid);
    if (!$well_thread) return TRUE;

    // hook model_well_cms_thread_delete_before.php

    // 删除tag
    $tagarr = xn_json_decode($well_thread['tag']);
    if ($tagarr) {
        $tagids = array();
        foreach ($tagarr as $key => $name) {
            if ($name) {
                $tagids[] = $key;
            }
        }

        well_cms_tag_data_delete($tagids, $tid);

        well_cms_tag_update($tagids, array('count-' => 1));
    }

    // hook model_well_cms_thread_delete_center.php

    // 删除flag
    $flagarr = explode(',', $well_thread['flag']);
    if ($flagarr) {

        well_cms_thread_flag_delete_tid($tid);

        $update = array();
        foreach ($flagarr as $flag) {
            // 轮播
            $flag == 1 AND $update['well_count_slides-'] = 1;
            // 头条
            $flag == 2 AND $update['well_count_headline-'] = 1;
            // 导读
            $flag == 3 AND $update['well_count_guide-'] = 1;
            // 推荐
            $flag == 4 AND $update['well_count_recommend-'] = 1;

            // hook model_well_cms_thread_delete_flag_count.php
        }

        forum__update($fid, $update);
    }

    // hook model_well_cms_thread_delete_after.php

    $r = well_cms__thread__delete($tid);

    // hook model_well_cms_thread_delete_end.php

    return $r;
}

function well_cms_thread_create($arr, &$pid)
{
    global $gid;
    $fid = $arr['fid'];
    $uid = $arr['uid'];
    $admin = $arr['admin'];
    $subject = $arr['subject'];
    $message = $arr['message'];
    $time = $arr['time'];
    $longip = $arr['longip'];
    $doctype = $arr['doctype'];
    $flagarr = $arr['flagarr'];
    $tagstr = $arr['tag'];
    $author_id = $arr['author_id'];
    $source_id = $arr['source_id'];

    // hook model_well_cms_thread_create_start.php

    # 论坛帖子数据，一页显示，不分页。
    $post = array(
        'tid' => 0,
        'isfirst' => 1,
        'uid' => $uid,
        'create_date' => $time,
        'userip' => $longip,
        'message' => $message,
        'doctype' => $doctype,
    );

    // hook model_cms_create_post.php

    $pid = post__create($post, $gid);
    if ($pid === FALSE) return FALSE;

    // hook model_well_cms_thread_create.php

    // 创建主题
    $thread = array(
        'fid' => $fid,
        'subject' => $subject,
        'uid' => $uid,
        'create_date' => $time,
        'last_date' => $time,
        'firstpid' => $pid,
        'lastpid' => $pid,
        'userip' => $longip
    );

    // hook model_well_cms_thread_post.php

    $tid = thread__create($thread);
    if ($tid === FALSE) {
        post__delete($pid);
        return FALSE;
    }

    // hook model_well_cms_thread_update_forum.php

    // 更新统计数据 后台内容不记录我的统计 只记录前台投稿
    $update = array('threads+' => 1);
    // hook model_well_cms_thread_create_user_update.php
    !$admin AND $uid AND user__update($uid, $update);

    $update = array('threads+' => 1, 'todaythreads+' => 1);

    if (!empty($flagarr)) {
        foreach ($flagarr as $flag) {
            // 轮播
            $flag == 1 AND $update['well_count_slides+'] = 1;
            // 头条
            $flag == 2 AND $update['well_count_headline+'] = 1;
            // 导读
            $flag == 3 AND $update['well_count_guide+'] = 1;
            // 推荐
            $flag == 4 AND $update['well_count_recommend+'] = 1;

            // hook model_well_cms_thread_flag_count.php
        }
    }

    // hook model_well_cms_thread_create_forum_update.php

    forum__update($fid, $update);

    // hook model_well_cms_thread_update_post.php

    // 关联
    post__update($pid, array('tid' => $tid), $tid);

    // hook model_well_cms_thread_mythread.php

    // 我参与的发帖 后台内容不记录我的发帖 只记录前台投稿
    !$admin AND $uid AND mythread_create($uid, $tid);

    // 关联主图 商城多预览图可以在此更新到post表或主题附加表
    $attach = well_cms_attach_assoc($pid, $tid);

    // hook model_well_cms_well_thread_assoc.php

    // 预处理tag
    $tagjson = well_cms_post_tag($tid, $tagstr);

    // 创建cms主题附加信息
    // hook model_well_cms_well_thread_create_start.php

    $well_thread = array('tid' => $tid, 'tag' => $tagjson, 'flag' => $arr['flag'], 'brief' => $arr['brief'], 'author_id' => $author_id, 'source_id' => $source_id);

    !empty($attach) AND $well_thread['mainpic_aid'] = $attach[1]['aid'];
    !empty($attach) AND $well_thread['mainpic'] = $attach[1]['picture'];

    // hook model_well_cms_well_thread_create_end.php

    well_cms__thread__create($well_thread);

    // 关联附件
    attach_assoc_post($pid);

    // 全站发帖数
    runtime_set('threads+', 1);
    runtime_set('todaythreads+', 1);

    // 更新板块信息。
    forum_list_cache_delete();

    // hook model_well_cms_thread_create_end.php

    return $tid;
}

// 移动主题处理wellcms数据
function well_cms_thread_move($fid, $tid)
{
    // hook model_well_cms_thread_move_start.php

    $well_thread = well_cms_thread_read($tid);
    if (!$well_thread) return TRUE;

    // 删除flag
    $flagarr = explode(',', $well_thread['flag']);
    if ($flagarr) {

        well_cms_thread_flag_delete_tid($tid);

        $update = array();
        foreach ($flagarr as $flag) {
            // 轮播
            $flag == 1 AND $update['well_count_slides-'] = 1;
            // 头条
            $flag == 2 AND $update['well_count_headline-'] = 1;
            // 导读
            $flag == 3 AND $update['well_count_guide-'] = 1;
            // 推荐
            $flag == 4 AND $update['well_count_recommend-'] = 1;

            // hook model_well_cms_thread_move_flag_count.php
        }

        forum__update($fid, $update);
    }

    // hook model_well_cms_thread_move_end.php
}

// hook model_well_cms_thread_end.php

?>