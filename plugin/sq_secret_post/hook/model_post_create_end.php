<?php exit;

// 如果是匿名回帖的话，隐藏该回帖信息
if ($arr['is_secret']) {
    $tablepre = $_SERVER['db']->tablepre;
    db_exec("UPDATE {$tablepre}thread SET `last_secret` = 1 WHERE tid = {$tid}");
}