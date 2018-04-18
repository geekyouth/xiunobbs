<?php

// ------------> 最原生的 CURD，无关联其他数据。
function well_cms__tag__create($arr = array())
{
    // hook model_well_cms__tag__create_start.php
    $r = db_insert('well_tag', $arr);
    // hook model_well_cms__tag__create_end.php
    return $r;
}

function well_cms__tag__update($cond = array(), $update = array())
{
    // hook model_well_cms__tag__update_start.php
    $r = db_update('well_tag', $cond, $update);
    // hook model_well_cms__tag__update_end.php
    return $r;
}

function well_cms__tag__read($cond = array())
{
    // hook model_well_cms__tag__read_start.php
    $r = db_find_one('well_tag', $cond);
    // hook model_well_cms__tag__read_end.php
    return $r;
}

function well_cms__tag__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms__tag__find_start.php
    $arr = db_find('well_tag', $cond, $orderby, $page, $pagesize, 'tagid');
    // hook model_well_cms__tag__find_end.php
    return $arr;
}

function well_cms_tag_delete($tagid)
{
    // hook model_well_cms_tag_delete_start.php
    $r = db_delete('well_tag', array('tagid' => $tagid));
    // hook model_well_cms_tag_delete_end.php
    return $r;
}

function well_cms_tag__count($cond = array())
{
    // hook model_well_cms_tag_count_start.php
    $n = db_count('well_tag', $cond);
    // hook model_well_cms_tag_count_end.php
    return $n;
}

function well_cms_tag_data_create($arr)
{
    // hook model_well_cms_tag_data_create_start.php
    $r = db_replace('well_tag_data', $arr);
    // hook model_well_cms_tag_data_create_end.php
    return $r;
}

function well_cms_tag_data_delete($tagid, $tid)
{
    // hook model_well_cms_tag_data_delete_start.php
    $r = db_delete('well_tag_data', array('tagid' => $tagid, 'tid' => $tid));
    // hook model_well_cms_tag_data_delete_end.php
    return $r;
}

function well_cms_tag__data__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_well_cms_tag__data__find_start.php
    $arr = db_find('well_tag_data', $cond, $orderby, $page, $pagesize, 'tid');
    // hook model_well_cms_tag__data__find_end.php
    return $arr;
}

function well_cms_tag__data__count($cond = array())
{
    // hook model_well_cms_tag__data__count_start.php
    $n = db_count('well_tag_data', $cond);
    // hook model_well_cms_tag__data__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_cms_tag_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model_well_cms_tag_create_start.php
    $r = well_cms__tag__create($arr);
    // hook model_well_cms_tag_create_end.php
    return $r;
}

// 标签名查询
function well_cms_tag_read_name($name)
{
    if (!$name) return array();
    // hook model_well_cms_tag_read_name_start.php
    $r = well_cms__tag__read(array('name' => $name));
    // hook model_well_cms_tag_read_name_end.php
    return $r;
}

// 标签tagid查询
function well_cms_tag_read_tagid($tagid)
{
    if (!$tagid) return array();
    // hook model_well_cms_tag_read_tagid_start.php
    $r = well_cms__tag__read(array('tagid' => $tagid));
    // hook model_well_cms_tag_read_tagid_end.php
    return $r;
}

function well_cms_tag_update($tagid, $update)
{
    if (!$tagid || empty($update)) return FALSE;
    // hook model_well_cms_tag_update_start.php
    $r = well_cms__tag__update(array('tagid' => $tagid), $update);
    // hook model_well_cms_tag_update_end.php
    return $r;
}

// 前台遍历标签 统计降序
function well_cms_tag_count_desc_find($page, $pagesize)
{
    // hook model_well_cms_tag_count_desc_find_start.php
    $arr = well_cms__tag__find(array('count' => array('>' => 0)), array('count' => -1), $page, $pagesize);
    // hook model_well_cms_tag_count_desc_find_end.php
    return $arr;
}

// 遍历标签 统计降序
function well_cms_tag_count_desc__find($page, $pagesize)
{
    // hook model_well_cms_tag_count_desc_find_start.php
    $arr = well_cms__tag__find(array(), array('count' => -1), $page, $pagesize);
    // hook model_well_cms_tag_count_desc_find_end.php
    return $arr;
}

function well_cms_tag_data_find($tagid, $page, $pagesize)
{
    // hook model_well_cms_tag_data_find_start.php
    $arr = well_cms_tag__data__find(array('tagid' => $tagid), array('tid' => -1), $page, $pagesize);
    // hook model_well_cms_tag_data_find_end.php
    return $arr;
}

function well_cms_tag_data_thread_find($tids, $page, $pagesize)
{
    // hook model_well_cms_tag_data_thread_find_start.php
    $threadlist = db_find('thread', array('tid' => $tids), array(), $page, $pagesize);
    // hook model_well_cms_tag_data_thread_find_end.php
    return $threadlist;
}

function well_cms_tag_count()
{
    // hook model_well_cms_tag_count_start.php
    $n = well_cms_tag__count(array('count' => array('>' => 0)));
    // hook model_well_cms_tag_count_end.php
    return $n;
}

function well_cms_tag_data_tagid_count($tagid)
{
    // hook model_well_cms_tag_data_count_start.php
    $n = well_cms_tag__data__count(array('tagid' => $tagid));
    // hook model_well_cms_tag_data_count_end.php
    return $n;
}

function well_cms_tag_safe_info($arr)
{
    // hook model_well_cms_tag_safe_info_start.php

    // hook model_well_cms_tag_safe_info_end.php
    return $arr;
}

//--------------------------cache--------------------------

// 首页&频道调用热门标签 返回tagid count name
function well_cache_hot_tag_find()
{
    // hook model_well_cache_hot_tag_find_start.php

    static $cache = array(); // 跨进程，需再加一层缓存： yac/redis/memcached/xcache/apc/
    if (isset($cache['hot_tag'])) return $cache['hot_tag'];

    $pagesize = $_SERVER['well_conf']['setting']['index']['tag'];
    $cache['hot_tag'] = well_cms_tag_count_desc_find(1, $pagesize);

    // hook model_well_cache_hot_tag_find_end.php

    return $cache['hot_tag'];
}

//--------------其他方法-------------

// 标签预处理
function well_cms_post_tag($tid, $data)
{
    $arr = trim($data);
    $arr = array_filter_empty(explode(' ', $arr));

    if (empty($arr)) return '';

    $arr = array_unique($arr);

    // $tags中的tagid和帖子tid 写入cms_tag_data
    // json创建帖子时入库主题附表
    return well_cms_tag_process($tid, $arr);
}

/*
 * 更新后传入空值，直接过滤了，但没有处理旧的统计数据
 * */
// 修改内容标签预处理 $newtag 字串, $oldtag旧的json数据
function well_cms_post_tag_update($tid, $newtag, $oldtag)
{
    if (empty($oldtag)) return well_cms_post_tag($tid, $newtag);

    $oldtag = xn_json_decode($oldtag);

    $newtag = trim($newtag);
    $arr = array_filter_empty(explode(' ', $newtag));
    $arr = array_unique($arr);

    $new_tags = $tags = array();
    if (!empty($arr)) {
        foreach ($arr as $tagname) {
            // 搜索数组键值，并返回对应的键名
            $key = array_search($tagname, $oldtag);
            if ($key === false) {
                // 创建新数组$new_tags
                $new_tags[] = $tagname;
            } else {
                // 保留的旧标签
                $tags[$key] = $tagname;
                // 销毁旧数组保留的标签 余下为需要删除的标签
                unset($oldtag[$key]);
            }
        }
    }

    // 删除标签
    if ($oldtag) {
        $tagids = array();
        foreach ($oldtag as $tagid => $tagname) {
            $tagids[] = $tagid;
        }

        if (!empty($tagids)) {
            well_cms_tag_update($tagids, array('count-' => 1));
            well_cms_tag_data_delete($tagids, $tid);
        }
    }

    return well_cms_tag_process($tid, $new_tags, $tags);
}

// 标签数据处理 $arr=新提交的数组 $tags=保留的旧标签
function well_cms_tag_process($tid = 0, $new_tags = array(), $tags = null)
{
    // 新标签处理入库
    if (!empty($new_tags)) {
        $tagids = array();
        $n = 0;
        foreach ($new_tags as $key => $name) {

            $n++;
            $name = trim($name);
            $name = htmlspecialchars($name);

            if ($name && $n <= 5) {

                // 查询标签表
                $r = well_cms_tag_read_name($name);
                if (!empty($r)) {
                    // 存在 count+1
                    $tagids[] = $r['tagid'];
                } else {
                    // 入库well_tag
                    $tagid = well_cms_tag_create(array('name' => $name, 'count' => 1));
                    if (!$tagid) message(-1, lang('well_create_tag_fail'));
                    $r = array('tagid' => $tagid, 'name' => $name);
                }

                // 入库well_tag_data
                if ($tid) well_cms_tag_data_create(array('tagid' => $r['tagid'], 'tid' => $tid));

                $tags[$r['tagid']] = $r['name'];
            }
        }

        !empty($tagids) AND well_cms_tag_update($tagids, array('count+' => 1));
    }

    if (!empty($tags)) {
        $tags = well_json_encode($tags);
    }

    return $tags;
}

?>
