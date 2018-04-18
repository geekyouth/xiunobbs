<?php

// ------------> 最原生的 CURD，无关联其他数据。
function well_cms__source__create($arr = array())
{
    // hook model_well_cms__source__create_start.php
    $r = db_insert('well_source', $arr);
    // hook model_well_cms__source__create_end.php
    return $r;
}

function well_cms__source__update($cond = array(), $update = array())
{
    // hook model_well_cms__source__update_start.php
    $r = db_update('well_source', $cond, $update);
    // hook model_well_cms__source__update_end.php
    return $r;
}

function well_cms__source__read($cond = array())
{
    // hook model_well_cms__source__read_start.php
    $r = db_find_one('well_source', $cond);
    // hook model_well_cms__source__read_end.php
    return $r;
}

function well_cms__source__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms__source__find_start.php
    $arr = db_find('well_source', $cond, $orderby, $page, $pagesize, 'id');
    // hook model_well_cms__source__find_end.php
    return $arr;
}

function well_cms__source__delete($cond = array())
{
    // hook model_well_cms__source__delete_start.php
    $r = db_delete('well_source', $cond);
    // hook model_well_cms__source__delete_end.php
    return $r;
}

function well_cms__source__count($cond = array())
{
    // hook model_well_cms__source__count_start.php
    $n = db_count('well_source', $cond);
    // hook model_well_cms__source__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_cms_source_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model_well_cms_source_create_start.php
    $r = well_cms__source__create($arr);
    // hook model_well_cms_source_create_end.php
    return $r;
}

// ID查询来源
function well_cms_source_read($id)
{
    if (!$id) return array();
    // hook model_well_cms_source_read_start.php
    $r = well_cms__source__read(array('id' => $id));
    // hook model_well_cms_source_read_end.php
    return $r;
}

// 来源名查询
function well_cms_source_read_name($name)
{
    if (!$name) return array();
    // hook model_well_cms_source_read_name_start.php
    $r = well_cms__source__read(array('name' => $name));
    // hook model_well_cms_source_read_name_end.php
    return $r;
}

// 主键更新
function well_cms_source_update($id, $update)
{
    if (!$id || empty($update)) return FALSE;
    // hook model_well_cms_source_update_start.php
    $r = well_cms__source__update(array('id' => $id), $update);
    // hook model_well_cms_source_update_end.php
    return $r;
}

// 主键删除
function well_cms_source_delete($id)
{
    // hook model_well_cms_source_delete_start.php
    $r = well_cms__source__delete(array('id' => $id));
    // hook model_well_cms_source_delete_end.php
    return $r;
}

// 查询所有来源
function well_cms_source_find($page, $pagesize)
{
    // hook model_well_cms_source_find_start.php
    $arrlist = well_cms__source__find(array(), array('id' => -1), $page, $pagesize);
    // hook model_well_cms_source_find_before.php
    if ($arrlist) {
        foreach ($arrlist as &$v) {
            well_cms_source_format($v);
        }
    }
    // hook model_well_cms_source_find_end.php
    return $arrlist;
}

// 统计所有
function well_cms_source_count()
{
    // hook model_well_cms_source_count_start.php
    $n = well_cms__source__count();
    // hook model_well_cms_source_count_end.php
    return $n;
}

function well_cms_source_safe_info($arr)
{
    // hook model_well_cms_source_safe_info_start.php

    // hook model_well_cms_source_safe_info_end.php
    return $arr;
}

function well_cms_source_format(&$v)
{
    // hook model_well_cms_source_format_start.php
    if (empty($v)) return;
    $v['name'] = stripslashes(htmlspecialchars_decode($v['name']));
    $v['link'] = stripslashes(htmlspecialchars_decode($v['link']));
    // hook model_well_cms_source_format_end.php
}

//--------------------------cache--------------------------
/*
 * 后台：将所有来源缓存cookie，根据使用频率排序
 * */
// sources rank
function well_admin_sources_key_values()
{
    $arr = well_admin_sources();
    $arr = well_array_rank_key($arr, 'rank', 'id', 'name');
    $arr[0] = lang('well_select');
    return $arr;
}

// get sources
function well_admin_sources()
{
    $arr = well_admin_get_cache_sources();
    if ($arr === NULL) {
        $arr = well_cms_source_find(1, 100);
        if ($arr) {
            $arr = arrlist_multisort($arr, 'count', FALSE);
            $arr = arrlist_change_key($arr, 'id');
            foreach ($arr as $k => &$v) {
                unset($arr[$k]['link']);
                unset($arr[$k]['count']);
                $v['rank'] = 0;
            }
            well_admin_set_cache_sources($arr);
        }
    }
    return $arr;
}

function well_admin_get_cache_sources()
{
    $arr = _COOKIE('well_admin_sources');
    if ($arr == NULL) {
        $arr = cache_get('well_admin_sources');
    } else {
        $arr = xn_json_decode($arr);
    }
    return $arr;
}

function well_admin_set_cache_sources($arr)
{
    if (!empty($arr)) {
        // 当前时间 $time - 8 * 3600
        $life = well_cache_eight_expired();
        well_cache_push_value('well_admin_sources', $arr, $life);
    }
}

?>
