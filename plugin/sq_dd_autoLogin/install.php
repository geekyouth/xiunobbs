<?php

/**
 * 钉钉登录
 * Skiychan <dev@skiy.net>
 * https://www.skiy.net/201804025057.html
 */

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "CREATE TABLE IF NOT EXISTS `{$tablepre}sq_dduser` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
	`u_id` int(11) NOT NULL COMMENT '用户ID',
    `dd_id` varchar(200) NOT NULL COMMENT '钉钉ID',
	`dd_name` varchar(50) NOT NULL COMMENT '钉钉名',
//     `create_date` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='钉钉登陆'";

db_exec($sql);