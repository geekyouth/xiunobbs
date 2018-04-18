<?php

// ------------> 最原生的 CURD，无关联其他数据。
function well_cms__author__create($arr = array())
{
    // hook model_well_cms__author__create_start.php
    $r = db_insert('well_author', $arr);
    // hook model_well_cms__author__create_end.php
    return $r;
}

function well_cms__author__update($cond = array(), $update = array())
{
    // hook model_well_cms__author__update_start.php
    $r = db_update('well_author', $cond, $update);
    // hook model_well_cms__author__update_end.php
    return $r;
}

function well_cms__author__read($cond = array())
{
    // hook model_well_cms__author__read_start.php
    $r = db_find_one('well_author', $cond);
    // hook model_well_cms__author__read_end.php
    return $r;
}

function well_cms__author__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms__author__find_start.php
    $arr = db_find('well_author', $cond, $orderby, $page, $pagesize, 'id');
    // hook model_well_cms__author__find_end.php
    return $arr;
}

function well_cms__author__delete($cond = array())
{
    // hook model_well_cms__author__delete_start.php
    $r = db_delete('well_author', $cond);
    // hook model_well_cms__author__delete_end.php
    return $r;
}

function well_cms__author__count($cond = array())
{
    // hook model_well_cms__author__count_start.php
    $n = db_count('well_author', $cond);
    // hook model_well_cms__author__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_cms_author_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model_well_cms_author_create_start.php
    $r = well_cms__author__create($arr);
    // hook model_well_cms_author_create_end.php
    return $r;
}

// ID查询作者
function well_cms_author_read($id)
{
    if (!$id) return array();
    // hook model_well_cms_author_read_start.php
    $r = well_cms__author__read(array('id' => $id));
    // hook model_well_cms_author_read_end.php
    return $r;
}

// 作者名查询
function well_cms_author_read_name($name)
{
    if (!$name) return array();
    // hook model_well_cms_author_read_name_start.php
    $r = well_cms__author__read(array('name' => $name));
    // hook model_well_cms_author_read_name_end.php
    return $r;
}

// 前台作者UID查询
function well_cms_author_read_uid($uid)
{
    if (!$uid) return array();
    // hook model_well_cms_author_read_uid_start.php
    $r = well_cms__author__read(array('uid' => $uid));
    // hook model_well_cms_author_read_uid_end.php
    return $r;
}

// 前台作者 检索
function well_cms_author_check($uid, $name)
{
    if (!$uid) return array();
    // hook model_well_cms_author_check_start.php
    $r = well_cms__author__read(array('uid' => $uid, 'name' => $name));
    // hook model_well_cms_author_check_end.php
    return $r;
}

// 主键更新
function well_cms_author_update($id, $update)
{
    if (!$id || empty($update)) return FALSE;
    // hook model_well_cms_author_update_start.php
    $r = well_cms__author__update(array('id' => $id), $update);
    // hook model_well_cms_author_update_end.php
    return $r;
}

// 主键删除
function well_cms_author_delete($id)
{
    // hook model_well_cms_author_delete_start.php
    $r = well_cms__author__delete(array('id' => $id));
    // hook model_well_cms_author_delete_end.php
    return $r;
}

// UID删除
function well_cms_author_delete_uid($uid)
{
    // hook model_well_cms_author_delete_uid_start.php
    $r = well_cms__author__delete(array('uid' => $uid));
    // hook model_well_cms_author_delete_uid_end.php
    return $r;
}

// 查询所有作者
function well_cms_all_author_find($page, $pagesize)
{
    // hook model_well_cms_all_author_find_start.php
    $arrlist = well_cms__author__find(array(), array('id' => -1), $page, $pagesize);
    // hook model_well_cms_all_author_find_before.php
    if ($arrlist) {
        foreach ($arrlist as &$v) {
            well_cms_author_format($v);
        }
    }
    // hook model_well_cms_all_author_find_end.php
    return $arrlist;
}

// 后台作者查询
function well_cms_author_find($page, $pagesize)
{
    // hook model_well_cms_author_find_start.php
    $arrlist = well_cms__author__find(array('uid' => 0), array(), $page, $pagesize);
    // hook model_well_cms_author_find_before.php
    if ($arrlist) {
        foreach ($arrlist as &$v) {
            well_cms_author_format($v);
        }
    }
    // hook model_well_cms_author_find_end.php
    return $arrlist;
}

// 统计所有
function well_cms_author_count()
{
    // hook model_well_cms_author_count_start.php
    $n = well_cms__author__count();
    // hook model_well_cms_author_count_end.php
    return $n;
}

function well_cms_author_format(&$v)
{
    // hook model_well_cms_author_format_start.php
    if (empty($v)) return;
    $v['name'] = stripslashes(htmlspecialchars_decode($v['name']));
    // hook model_well_cms_author_format_end.php
}

function well_cms_author_safe_info($arr)
{
    // hook model_well_cms_author_safe_info_start.php

    // hook model_well_cms_author_safe_info_end.php
    return $arr;
}

//--------------------------cache--------------------------
/*
 * 后台：将所有作者缓存cookie，根据使用频率排序
 * $fidarr = arrlist_key_values($channellist, 'fid', 'name');
 * */
// authors rank
function well_admin_authors_key_values()
{
    $arr = well_admin_authors();
    $arr = well_array_rank_key($arr, 'rank', 'id', 'name');
    $arr[0] = lang('well_select');
    return $arr;
}

// get authors
function well_admin_authors()
{
    $arr = well_admin_get_cache_authors();
    if ($arr === NULL) {
        $arr = well_cms_author_find(1, 100);
        if ($arr) {
            $arr = arrlist_multisort($arr, 'count', FALSE);
            $arr = arrlist_change_key($arr, 'id');
            foreach ($arr as $k => &$v) {
                unset($arr[$k]['uid']);
                unset($arr[$k]['count']);
                $v['rank'] = 0;
            }
            well_admin_set_cache_authors($arr);
        }
    }
    return $arr;
}

function well_admin_get_cache_authors()
{
    $arr = _COOKIE('well_admin_authors');
    if ($arr == NULL) {
        $arr = cache_get('well_admin_authors');
    } else {
        $arr = xn_json_decode($arr);
    }
    return $arr;
}

function well_admin_set_cache_authors($arr)
{
    if (!empty($arr)) {
        $life = well_cache_eight_expired();
        well_cache_push_value('well_admin_authors', $arr, $life);
    }
}

?>
