<?php

defined('DEBUG') OR exit('Forbidden');

/*
$tablepre = $db->tablepre;
$sql = "
ALTER TABLE {$tablepre}post DROP INDEX `uid`;
";
$r = db_exec($sql);
*/

// 删除插件配置
setting_delete('haya_post_info'); 

?>
