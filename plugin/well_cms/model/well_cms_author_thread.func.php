<?php

// ------------> 最原生的 CURD，无关联其他数据。
function well_cms__author_thread__create($arr = array())
{
    // hook model_well_cms__author_thread__create_start.php
    $r = db_insert('well_author_thread', $arr);
    // hook model_well_cms__author_thread__create_end.php
    return $r;
}

function well_cms__author_thread__update($cond = array(), $update = array())
{
    // hook model_well_cms__author_thread__update_start.php
    $r = db_update('well_author_thread', $cond, $update);
    // hook model_well_cms__author_thread__update_end.php
    return $r;
}

function well_cms__author_thread__read($cond = array())
{
    // hook model_well_cms__author_thread__read_start.php
    $r = db_find_one('well_author_thread', $cond);
    // hook model_well_cms__author_thread__read_end.php
    return $r;
}

function well_cms__author_thread__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms__author_thread__find_start.php
    $arr = db_find('well_author_thread', $cond, $orderby, $page, $pagesize, 'id');
    // hook model_well_cms__author_thread__find_end.php
    return $arr;
}

function well_cms__author_thread__delete($cond = array())
{
    // hook model_well_cms__author_thread__delete_start.php
    $r = db_delete('well_author_thread', $cond);
    // hook model_well_cms__author_thread__delete_end.php
    return $r;
}

function well_cms__author_thread__count($cond = array())
{
    // hook model_well_cms__author_thread__count_start.php
    $n = db_count('well_author_thread', $cond);
    // hook model_well_cms__author_thread__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_cms_author_thread_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model_well_cms_author_thread_create_start.php
    $r = well_cms__author_thread__create($arr);
    // hook model_well_cms_author_thread_create_end.php
    return $r;
}

// ID查询作者主题tid
function well_cms_author_thread_read($author_id)
{
    if (!$author_id) return array();
    // hook model_well_cms_author_thread_read_start.php
    $r = well_cms__author_thread__read(array('author_id' => $author_id));
    // hook model_well_cms_author_thread_read_end.php
    return $r;
}

// 前台作者UID查询 未加索引 需要时自行加索引
/*function well_cms_author_thread_read_uid($uid)
{
    if (!$uid) return array();
    // hook model_well_cms_author_thread_read_uid_start.php
    $r = well_cms__author_thread__read(array('uid' => $uid));
    // hook model_well_cms_author_thread_read_uid_end.php
    return $r;
}*/

// 主键更新
function well_cms_author_thread_update($author_id, $update)
{
    if (!$author_id || empty($update)) return FALSE;
    // hook model_well_cms_author_thread_update_start.php
    $r = well_cms__author_thread__update(array('author_id' => $author_id), $update);
    // hook model_well_cms_author_thread_update_end.php
    return $r;
}

// 主键删除
function well_cms_author_thread_delete($author_id)
{
    if (!$author_id) return TRUE;
    // hook model_well_cms_author_thread_delete_start.php
    $r = well_cms__author_thread__delete(array('author_id' => $author_id));
    // hook model_well_cms_author_thread_delete_end.php
    return $r;
}

// 删除作者主题
function well_cms_author_thread_delete_tid($author_id, $tid)
{
    if (!$author_id) return TRUE;
    // hook model_well_cms_author_thread_delete_tid_start.php
    $r = well_cms__author_thread__delete(array('author_id' => $author_id, 'tid' => $tid));
    // hook model_well_cms_author_thread_delete_tid_end.php
    return $r;
}

// UID删除
function well_cms_author_thread_delete_uid($uid)
{
    // hook model_well_cms_author_thread_delete_uid_start.php
    $r = well_cms__author_thread__delete(array('uid' => $uid));
    // hook model_well_cms_author_thread_delete_uid_end.php
    return $r;
}

// 根据作者author_id 查询作者 所有主题
function well_cms_author_thread_find($author_id, $page, $pagesize)
{
    if (!$author_id) return FALSE;

    $orderby = array('tid' => -1);
    $threadlist = array();

    // hook model_well_cms_author_thread_find_start.php

    $arrlist = well_cms__author_thread__find(array('author_id' => $author_id), $orderby, $page, $pagesize);

    // hook model_well_cms_author_thread_find_before.php

    if ($arrlist) {

        $tidarr = arrlist_values($arrlist, 'tid');
        $threadlist = db_find('thread', array('tid' => $tidarr), $orderby, $page, $pagesize, 'tid');

        // hook model_well_cms_author_thread_find_before.php

        foreach ($threadlist as &$v) {
            well_cms_author_thread_format($v);
        }

        // 主题 URL 格式化
        well_thread_url_format($threadlist);
    }

    // hook model_well_cms_author_thread_find_end.php

    return $threadlist;
}

// 统计作者author_id 主题数
function well_cms_author_thread_count($author_id)
{
    // hook model_well_cms_author_thread_count_start.php
    $n = well_cms__author_thread__count(array('author_id' => $author_id));
    // hook model_well_cms_author_thread_count_end.php
    return $n;
}

function well_cms_author_thread_format(&$v)
{
    // hook model_well_cms_author_thread_format_start.php
    if (empty($v)) return;

    // hook model_well_cms_author_thread_format_end.php
}

function well_cms_author_thread_safe_info($arr)
{
    // hook model_well_cms_author_thread_safe_info_start.php

    // hook model_well_cms_author_thread_safe_info_end.php
    return $arr;
}

//--------------------------业务相关--------------------------

function well_cms_author_id($author_id = 0, $author_name = NULL)
{
    if (!$author_id && !$author_name) return FALSE;

    $cachearr = well_admin_get_cache_authors();
    if ($author_name) {

        $r = well_cms_author_read_name($author_name);

        if (!empty($r)) {
            //更新缓存
            isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
            $author_id = $r['id'];

        } else {
            // 创建 返回ID入库 加入缓存
            $author_id = well_cms_author_create(array('name' => $author_name, 'count' => 1));
            // 加入缓存
            $cachearr[$author_id] = array('id' => $author_id, 'name' => $author_name, 'rank' => 1);
        }

    } elseif ($author_id) {

        $r = well_cms_author_read($author_id);
        empty($r) AND message(-1, lang('well_author_empty_not_exist'));

        well_cms_author_update($author_id, array('count+' => 1));

        //更新缓存
        isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
    }
    // 缓存
    well_admin_set_cache_authors($cachearr);

    return $author_id;
}

function well_cms_author_id_update($author_id = 0, $old_author_id = 0, $author_name = NULL)
{
    if (!$author_id && !$author_name) return FALSE;

    $cachearr = well_admin_get_cache_authors();
    if ($author_name) {

        $r = well_cms_author_read_name($author_name);

        if (!empty($r)) {

            if ($r['id'] == $old_author_id) return FALSE;

            //更新缓存
            isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
            $author_id = $r['id'];

        } else {
            // 创建 返回ID入库 加入缓存
            $author_id = well_cms_author_create(array('name' => $author_name, 'count' => 1));
            // 加入缓存
            $cachearr[$author_id] = array('id' => $author_id, 'name' => $author_name, 'rank' => 1);
            $old_author_id AND well_cms_author_update($old_author_id, array('count-' => 1));
        }

    } elseif ($author_id) {

        $r = well_cms_author_read($author_id);
        well_cookie_set('well_admin_authors', '', 0);
        empty($r) AND message(-1, lang('well_author_empty_not_exist'));

        well_cms_author_update($author_id, array('count+' => 1));
        $old_author_id AND well_cms_author_update($old_author_id, array('count-' => 1));

        //更新缓存
        isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
    }

    // 旧的ID统计-1
    isset($cachearr[$old_author_id]) AND $cachearr[$old_author_id]['rank'] -= 1;

    // 缓存
    well_admin_set_cache_authors($cachearr);

    $old_author_id AND well_cms_author_thread_update($old_author_id, array('author_id' => $author_id));

    return $author_id;
}

?>