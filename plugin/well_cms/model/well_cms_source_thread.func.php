<?php

// ------------> 最原生的 CURD，无关联其他数据。
function well_cms__source_thread__create($arr = array())
{
    // hook model_well_cms__source_thread__create_start.php
    $r = db_insert('well_source_thread', $arr);
    // hook model_well_cms__source_thread__create_end.php
    return $r;
}

function well_cms__source_thread__update($cond = array(), $update = array())
{
    // hook model_well_cms__source_thread__update_start.php
    $r = db_update('well_source_thread', $cond, $update);
    // hook model_well_cms__source_thread__update_end.php
    return $r;
}

function well_cms__source_thread__read($cond = array())
{
    // hook model_well_cms__source_thread__read_start.php
    $r = db_find_one('well_source_thread', $cond);
    // hook model_well_cms__source_thread__read_end.php
    return $r;
}

function well_cms__source_thread__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms__source_thread__find_start.php
    $arr = db_find('well_source_thread', $cond, $orderby, $page, $pagesize, 'id');
    // hook model_well_cms__source_thread__find_end.php
    return $arr;
}

function well_cms__source_thread__delete($cond = array())
{
    // hook model_well_cms__source_thread__delete_start.php
    $r = db_delete('well_source_thread', $cond);
    // hook model_well_cms__source_thread__delete_end.php
    return $r;
}

function well_cms__source_thread__count($cond = array())
{
    // hook model_well_cms__source_thread__count_start.php
    $n = db_count('well_source_thread', $cond);
    // hook model_well_cms__source_thread__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_cms_source_thread_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model_well_cms_source_thread_create_start.php
    $r = well_cms__source_thread__create($arr);
    // hook model_well_cms_source_thread_create_end.php
    return $r;
}

// ID查询来源
function well_cms_source_thread_read($source_id)
{
    if (!$source_id) return array();
    // hook model_well_cms_source_thread_read_start.php
    $r = well_cms__source_thread__read(array('source_id' => $source_id));
    // hook model_well_cms_source_thread_read_end.php
    return $r;
}

// 主键更新
function well_cms_source_thread_update($source_id, $update)
{
    if (!$source_id || empty($update)) return FALSE;
    // hook model_well_cms_source_thread_update_start.php
    $r = well_cms__source_thread__update(array('source_id' => $source_id), $update);
    // hook model_well_cms_source_thread_update_end.php
    return $r;
}

// 删除来源主题
function well_cms_source_thread_delete_tid($source_id, $tid)
{
    if (!$source_id) return TRUE;
    // hook model_well_cms_source_thread_delete_tid_start.php
    $r = well_cms__source_thread__delete(array('source_id' => $source_id, 'tid' => $tid));
    // hook model_well_cms_source_thread_delete_tid_end.php
    return $r;
}

// 主键删除
function well_cms_source_thread_delete($source_id)
{
    if (!$source_id) return TRUE;
    // hook model_well_cms_source_thread_delete_start.php
    $r = well_cms__source_thread__delete(array('source_id' => $source_id));
    // hook model_well_cms_source_thread_delete_end.php
    return $r;
}

// 查询来源 所有主题 tid
function well_cms_source_thread_find($source_id, $page, $pagesize)
{
    if (!$source_id) return FALSE;

    $orderby = array('tid' => -1);
    $threadlist = array();

    // hook model_well_cms_source_thread_find_start.php
    $arrlist = well_cms__source_thread__find(array('source_id' => $source_id), $orderby, $page, $pagesize);
    // hook model_well_cms_source_thread_find_before.php

    if ($arrlist) {

        $tidarr = arrlist_values($arrlist, 'tid');
        $threadlist = db_find('thread', array('tid' => $tidarr), $orderby, $page, $pagesize, 'tid');

        // hook model_well_cms_source_thread_find_before.php

        foreach ($threadlist as &$v) {
            well_cms_author_thread_format($v);
        }

        // 主题 URL 格式化
        well_thread_url_format($threadlist);
    }

    // hook model_well_cms_source_thread_find_end.php

    return $threadlist;
}

// 统计来源source_id 主题数
function well_cms_source_thread_count($source_id)
{
    // hook model_well_cms_source_thread_count_start.php
    $n = well_cms__source_thread__count(array('source_id' => $source_id));
    // hook model_well_cms_source_thread_count_end.php
    return $n;
}

function well_cms_source_thread_format(&$v)
{
    // hook model_well_cms_source_thread_format_start.php
    if (empty($v)) return;

    // hook model_well_cms_source_thread_format_end.php
}

function well_cms_source_thread_safe_info($arr)
{
    // hook model_well_cms_source_thread_safe_info_start.php

    // hook model_well_cms_source_thread_safe_info_end.php
    return $arr;
}

//--------------------------业务相关--------------------------

function well_cms_source_id($source_id = 0, $source_name = '', $source_link = '')
{
    if (!$source_id && !$source_name) return FALSE;

    $cachearr = well_admin_get_cache_sources();
    if ($source_name) {

        $r = well_cms_source_read_name($source_name);

        if (!empty($r)) {
            //更新缓存
            isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
            $source_id = $r['id'];

        } else {
            // 创建 返回ID入库 加入缓存
            $source_id = well_cms_source_create(array('name' => $source_name,'link' => $source_link, 'count' => 1));
            // 加入缓存
            $cachearr[$source_id] = array('id' => $source_id, 'name' => $source_name, 'rank' => 1);
        }

    } elseif ($source_id) {

        $r = well_cms_source_read($source_id);
        well_cookie_set('well_admin_sources', '', 0);
        empty($r) AND message(-1, lang('well_source_name_not_exist'));

        well_cms_source_update($source_id, array('count+' => 1));

        //更新缓存
        isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
    }
    // 缓存
    well_admin_set_cache_sources($cachearr);

    return $source_id;
}

function well_cms_source_id_update($source_id = 0, $old_source_id = 0, $source_name = '', $source_link = '')
{
    if (!$source_id && !$source_name) return FALSE;

    $cachearr = well_admin_get_cache_sources();
    if ($source_name) {

        $r = well_cms_source_read_name($source_name);

        if (!empty($r)) {

            if ($r['id'] == $old_source_id) return FALSE;

            //更新缓存
            isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
            $source_id = $r['id'];

        } else {
            // 创建 返回ID入库 加入缓存
            $source_id = well_cms_source_create(array('name' => $source_name,'link' => $source_link, 'count' => 1));
            // 加入缓存
            $cachearr[$source_id] = array('id' => $source_id, 'name' => $source_name, 'rank' => 1);
            well_cms_source_update($old_source_id, array('count-' => 1));
        }

    } elseif ($source_id) {

        $r = well_cms_source_read($source_id);
        empty($r) AND message(-1, lang('well_source_name_not_exist'));

        well_cms_source_update($source_id, array('count+' => 1));
        $old_source_id AND well_cms_source_update($old_source_id, array('count-' => 1));

        //更新缓存
        isset($cachearr[$r['id']]) AND $cachearr[$r['id']]['rank'] += 1;
    }

    //更新缓存
    isset($cachearr[$old_source_id]) AND $cachearr[$old_source_id]['rank'] -= 1;

    // 缓存
    well_admin_set_cache_sources($cachearr);

    $old_source_id AND well_cms_source_thread_update($old_source_id, array('source_id' => $source_id));

    return $source_id;
}

?>
