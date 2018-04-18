<?php

// cache + 1
// well_cache_increase('well_admin_authors', $arr, $_key) array[$_key]
function well_cache_rank_increase($key, $arr, $_key, $increase = 1)
{
    if (!empty($arr)) {
        $arr[$_key]['rank'] += $increase;
        $life = well_cache_eight_expired();
        well_cache_push_value($key, $arr, $life);
    }
}

// cache - 1
function well_cache_rank_decrease($key, $arr, $_key, $decrease = 1)
{
    if (!empty($arr)) {
        $arr[$_key]['rank'] -= $decrease;
        $life = well_cache_eight_expired();
        well_cache_push_value($key, $arr, $life);
    }
}

// set up cache
function well_cache_push_value($key, $value, $life)
{
    $conf = $_SERVER['conf'];
    well_cookie_set($key, $value, $life);
    $conf['cache']['type'] != 'mysql' AND cache_set($key, $value, $life);
}


?>