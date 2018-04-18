<?php

function well_cookie_set($key, $value, $life = 8640000)
{
    $time = $_SERVER['time'];
    if (is_array($value)) $value = well_json_encode($value);
    setcookie($key, $value, $time + $life, '');
}

function well_cookie_empty($key)
{
    $time = $_SERVER['time'];
    setcookie($key, '', $time - 86400, '');
}

?>