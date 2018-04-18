<?php

/*
 * 创建主题成功返回tid创建flag数据
 * 更新：修改前查询，提交后比对是否更新，已取消的属性在数据库中删除
 * 查询版块下相应属性的主题
 * */

// hook model_well_cms_thread_flag_start.php

function well_cms_thread_flag_create($arr = array())
{
    // hook model_well_cms_thread_flag_create_start.php
    $r = db_insert('well_thread_flag', $arr);
    // hook model_well_cms_thread_flag_create_end.php
    return $r;
}

function well_cms_thread__flag__update($cond = array(), $update = array())
{
    if (empty($arr)) return TRUE;
    // hook model_well_cms_thread__flag__update_start.php
    $r = db_update('well_thread_flag', $cond, $update);
    // hook model_well_cms_thread__flag__update_end.php
    return $r;
}

function well_cms__delete__flag($cond = array())
{
    // hook model_well_cms__delete__flag_start.php
    $r = db_delete('well_thread_flag', $cond);
    // hook model_well_cms__delete__flag_end.php
    return $r;
}

// 查询 1.flag ORDER BY tid   2.flag fid ORDER BY tid
function well_cms_thread__flag__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = '', $col = array())
{
    // hook model_well_cms_thread__flag__find_start.php
    $arrlist = db_find('well_thread_flag', $cond, $orderby, $page, $pagesize, $key, $col);
    // hook model_well_cms_thread__flag__find_end.php
    return $arrlist;
}

// 删除 某版块下 某属性主题
function well_cms_delete_fid_flag($flag, $fid)
{
    // hook model_well_cms_delete_fid_flag_start.php
    $r = well_cms__delete__flag(array('flag' => $flag, 'fid' => $fid));
    // hook model_well_cms_delete_fid_flag_end.php
    return $r;
}

// 删除 主题 所有属性
function well_cms_thread_flag_delete_tid($tid)
{
    // hook model_well_cms_thread_flag_delete_tid_start.php
    $r = well_cms__delete__flag(array('tid' => $tid));
    // hook model_well_cms_thread_flag_delete_tid_end.php
    return $r;
}

// 删除 主题 单条属性
function well_cms_thread_flag_delete_one($flag, $tid)
{
    // hook model_well_cms_thread_flag_delete_one_start.php
    $r = well_cms__delete__flag(array('flag' => $flag, 'tid' => $tid));
    // hook model_well_cms_thread_flag_delete_one_end.php
    return $r;
}

// 更新主题属性
function well_cms_thread_flag_update($flag, $tid, $arr)
{
    if (empty($arr)) return TRUE;
    // hook model_well_cms_thread_flag_update_start.php
    $r = well_cms_thread__flag__update(array('flag' => $flag, 'tid' => $tid), $arr);
    // hook model_well_cms_thread_flag_update_end.php
    return $r;
}

// 查询主题属性 返回 tidarr
function well_cms_thread_flag_tid_find($cond, $orderby, $page, $pagesize)
{
    // hook model_well_cms_thread_flag_tid_find_start.php

    $arrlist = well_cms_thread__flag__find($cond, $orderby, $page, $pagesize, 'tid');
    if (empty($arrlist)) return array();

    $tidarr = arrlist_values($arrlist, 'tid');

    // hook model_well_cms_thread_flag_tid_find_end.php
    return $tidarr;
}

//--------------------------cache--------------------------

// 缓存 某属性下 主题 flag ORDER BY tid
function well_cms_cache_thread_flag($flag, $page = 1, $pagesize = 20)
{
    // hook model_well_cms_cache_thread_flag_start.php

    static $cache = array(); // 跨进程，需再加一层缓存： yac/redis/memcached/xcache/apc/
    if (isset($cache[$flag])) return $cache[$flag];

    $cache[$flag] = well_cms_thread_flag_find($flag, $page, $pagesize);

    // hook model_well_cms_cache_thread_flag_end.php

    return $cache[$flag];
}

// 缓存 某版块下 某属性 主题 flag fid ORDER BY tid
function well_cms_cache_forum_thread_flag($flag, $fid, $page = 1, $pagesize = 20)
{
    $key = $flag . '_' . $fid;

    // hook model_well_cms_cache_forum_thread_flag_start.php

    static $cache = array(); // 跨进程，需再加一层缓存： yac/redis/memcached/xcache/apc/
    if (isset($cache[$key])) return $cache[$key];

    $cache[$key] = well_cms_forum_thread_flag_find($flag, $fid, $page, $pagesize);

    // hook model_well_cms_cache_forum_thread_flag_end.php

    return $cache[$key];
}

// 缓存 某频道下 某属性 主题 flag fid ORDER BY tid
function well_cms_cache_channel_thread_flag($flag, $fup, $page = 1, $pagesize = 20)
{
    $key = $flag . '_' . $fup;

    // hook model_well_cms_cache_channel_thread_flag_start.php

    static $cache = array(); // 跨进程，需再加一层缓存： yac/redis/memcached/xcache/apc/
    if (isset($cache[$key])) return $cache[$key];

    $cache[$key] = well_cms_fup_thread_flag_find($flag, $fup, $page, $pagesize);

    // hook model_well_cms_cache_channel_thread_flag_end.php

    return $cache[$key];
}

//--------------------------强相关数据--------------------------

// 查询 某属性下 主题 flag ORDER BY tid
function well_cms_thread_flag_find($flag, $page = 1, $pagesize = 20)
{
    // hook model_well_cms_thread_flag_find_start.php

    $cond = array('flag' => $flag);

    $threadlist = well_cms_flag_thread_find($cond, $page, $pagesize);

    // hook model_well_cms_thread_flag_find_end.php

    return $threadlist;
}

// 查询 #某版块下# 某属性 主题 flag fid ORDER BY tid
function well_cms_forum_thread_flag_find($flag, $fid, $page = 1, $pagesize = 20)
{
    // hook model_well_cms_thread_flag_find_start.php

    $cond = array('flag' => $flag, 'fid' => $fid);

    $threadlist = well_cms_flag_thread_find($cond, $page, $pagesize);

    // hook model_well_cms_thread_flag_find_end.php

    return $threadlist;
}

// 查询 #某频道下# 某属性 主题 flag fup ORDER BY tid
function well_cms_fup_thread_flag_find($flag, $fup, $page = 1, $pagesize = 20)
{
    // hook model_well_cms_fup_thread_flag_find_start.php

    $cond = array('flag' => $flag, 'fup' => $fup);

    $threadlist = well_cms_flag_thread_find($cond, $page, $pagesize);

    // hook model_well_cms_fup_thread_flag_find_end.php

    return $threadlist;
}

//--------------------------其他方法--------------------------

// 查询属性主题 返回主题数据
function well_cms_flag_thread_find($cond, $page, $pagesize)
{
    // hook model_well_cms_flag_thread_find_start.php

    $orderby = array('tid' => -1);

    // hook model_well_cms_thread_flag_tids_start.php

    $tidarr = well_cms_thread_flag_tid_find($cond, $orderby, $page, $pagesize);

    if (empty($tidarr)) return array();

    // hook model_well_cms_thread_flag_tids_end.php

    $threadlist = well_cms_thread_find($tidarr, $orderby, $page, $pagesize);

    // hook model_well_cms_flag_thread_find_end.php

    return $threadlist;
}

// 更新 & 新增 & 删除 旧标题属性
function well_cms_thread_flag_delete_old($fid, $fup, $tid, $old_flag = array(), $new_flags = array())
{
    // hook model_well_cms_thread_flag_delete_old_start.php

    $fid = intval($fid);
    $tid = intval($tid);
    if (!$fid || !$tid) return FALSE;

    $new_flags = explode(',', $new_flags);
    $new_flags = array_filter_empty($new_flags);

    $old_flag = explode(',', $old_flag);
    $old_flag = array_filter_empty($old_flag);

    // hook model_well_cms_thread_flag_delete_old_before.php

    if ($new_flags) {
        // 返回新增的标题属性
        $arr = well_array_diff($new_flags, $old_flag);

        // hook model_well_cms_thread_flag_delete_old_center.php
        if ($arr) {
            foreach ($arr as $flag) {
                well_cms_thread_flag_create(array('fid' => $fid, 'fup' => $fup, 'tid' => $tid,'flag' => $flag));
            }
        }

        // 返回删除的标题属性
        $arr = well_array_diff($old_flag, $new_flags);

        // hook model_well_cms_thread_flag_delete_old_after.php

        if ($arr) {
            foreach ($arr as $flag) {
                well_cms_thread_flag_delete_one($flag, $tid);
            }
        }
    }

    // 删除主题下所有属性
    if ($old_flag && !$new_flags) {
        well_cms_thread_flag_delete_tid($tid);
    }

    // hook model_well_cms_thread_flag_delete_old_end.php

    return TRUE;
}

// 转flag字串为数组
function well_cms_thread_flag_array($flagstr)
{
    // hook well_cms_thread_flag_array_start.php

    $arr = explode(',', $flagstr);
    $arr = array_filter_empty($arr);

    // hook well_cms_thread_flag_array_before.php

    $flagarr = array();
    if ($arr) {
        foreach ($arr as $flag) {
            // 轮播
            $flag == 1 AND $flagarr[$flag] = lang('well_slides');
            // 头条
            $flag == 2 AND $flagarr[$flag] = lang('well_headline');
            // 导读
            $flag == 3 AND $flagarr[$flag] = lang('well_guide');
            // 推荐
            $flag == 4 AND $flagarr[$flag] = lang('well_recommend');

            // hook well_cms_thread_flag_array_after.php
        }
    }

    // hook well_cms_thread_flag_array_end.php

    return $flagarr;
}

// hook model_well_cms_thread_flag_end.php

?>