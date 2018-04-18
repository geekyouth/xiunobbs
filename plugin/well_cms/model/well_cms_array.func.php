<?php

// edit array key
function well_array_key_edit($arr = array(), $key = NULL, $new_key = NULL)
{
    if (empty($arr)) return array();
    if (count($arr) == count($arr, 1)) {
        // one dimensional array
        isset($arr[$key]) AND $arr[$new_key] = $arr[$key];
    } else {
        // two dimensional array
        foreach ($arr as $k => &$v) {
            if (isset($v[$key])) {
                $v[$new_key] = $v[$key];
                unset($v[$key]);
            }
        }
    }
    return $arr;
}

// delete array key _key
function well_array_key_delete($arr = array(), $key = NULL, $_key = NULL)
{
    if (empty($arr)) return array();
    if (count($arr) == count($arr, 1)) {
        // one dimensional array
        if (isset($arr[$key])) unset($arr[$key]);
    } else {
        // two dimensional array
        if (isset($arr[$key]) && $_key == NULL) {
            unset($arr[$key]);
        } else {
            foreach ($arr as $k => &$v) {
                if (isset($v[$_key])) {
                    unset($v[$_key]);
                }
            }
        }
    }
    return $arr;
}

// comparison array key value,return difference key
function well_array_diff($arr1, $arr2)
{
    if (!is_array($arr1) || !is_array($arr2)) return FALSE;
    return array_diff($arr1, $arr2);
}

function well_array_to_str($arr)
{
    $str = '';
    if (!empty($arr)) {
        foreach ($arr as $v) {
            $str .= $v . '|';
        }
        $str = trim($str, '|');
    }
    return $str;
}

function well_str_to_array($str = NULL, $sign = NULL)
{
    $arr = array();
    if ($str && $sign) {
        $arr = explode($sign, $str);
        $arr = array_filter_empty($arr);
        if (empty($arr)) {
            $arr = array();
        }
    }
    return $arr;
}

function well_thread_tag_to_str($arr)
{
    $str = '';
    !is_array($arr) AND $arr = xn_json_decode($arr);
    if (!empty($arr)) {
        foreach ($arr as $v) {
            $str .= $v . ',';
        }
        $str = trim($str, ',');
    }
    return $str;
}

// 获取主图
function well_mainpic($arr)
{
    $mainpic = array_value($arr, 'mainpic');
    if ($mainpic && file_exists(APP_PATH . $mainpic)) return $mainpic;
    return './plugin/well_cms/view/image/nopic.png';
}

// 倒叙 二维关联数整理一维关联数组 col排序列 关联key=>value
function well_array_rank_key($arr = array(), $col = NULL, $key = NULL, $value = NULL)
{
    if (!empty($arr) && $col && $key && $value) {
        $arr = arrlist_multisort($arr, $col, FALSE);
        $arr = arrlist_key_values($arr, $key, $value);
    }
    return $arr;
}

?>