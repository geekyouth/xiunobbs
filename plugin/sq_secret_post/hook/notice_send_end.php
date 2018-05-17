<?php exit;
$isSecret = param('is_secret');
$isSecret = $isSecret == 'on' ? 1 : 0;
if ($isSecret) {
    $tablepre = $_SERVER['db']->tablepre;
    db_exec("UPDATE {$tablepre}notice SET `is_secret` = 1 WHERE nid = {$nid}");
}