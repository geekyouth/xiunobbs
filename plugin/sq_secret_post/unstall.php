<?php

/**
 * 帖子收藏插件 卸载程序
 *
 * @create 2018-1-25
 * @author deatil
 */

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

// db_exec("DROP TABLE IF EXISTS {$tablepre}haya_favorite;");

db_exec("ALTER TABLE {$tablepre}thread DROP COLUMN is_secret;");
db_exec("ALTER TABLE {$tablepre}thread DROP COLUMN last_secret;");
db_exec("ALTER TABLE {$tablepre}post DROP COLUMN is_secret;");

$noticeConf = file_get_contents(APP_PATH . '/plugin/huux_notice/conf.json');
$noticeConf = json_decode($noticeConf, true);
if($noticeConf['installed']) {
    db_exec("ALTER TABLE {$tablepre}notice DROP COLUMN is_secret;");
}

// // 删除插件配置
// setting_delete('haya_favorite'); 

// // 清空热门缓存
// cache_delete('haya_favorite_favorites');

?>