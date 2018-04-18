<?php
/*
 * Copyright (C) 燃烧的冰 81340116@qq.com
 */

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "DROP TABLE IF EXISTS {$tablepre}well_thread;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_thread_flag;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_tag;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_tag_data;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_author;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_author_thread;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_source;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}well_source_thread;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}user DROP `well_token`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_pagesize`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_nav_display`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_comment`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_display`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_news`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_headlines`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_slides`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_headline`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_guide`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_recommend`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_display`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_list_news`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_list_headlines`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_list_recommends`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_news`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_headlines`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_model`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_fup`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_son`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_type`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_forum_type`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_alias`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_tpl`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_tpl_class`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_tpl_show`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_count_headline`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_count_guide`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_count_slides`;";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum DROP `well_count_recommend`;";
$r = db_exec($sql);

xn_unlink('../view/img/well_logo.png');
clearstatcache();

setting_delete('well_conf');
//$r === false AND message(-1, '卸载WellCMS插件失败');

?>