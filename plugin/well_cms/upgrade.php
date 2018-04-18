<?php

!defined('DEBUG') AND exit('Forbidden');

include APP_PATH . 'plugin/well_cms/model/well_check_db.func.php';

$tablepre = $db->tablepre;

if (!well_db_find_field("{$tablepre}user", 'well_token')) {
    $sql = "ALTER TABLE {$tablepre}user ADD COLUMN well_token char(60) NOT NULL DEFAULT '';";
    $r = db_exec($sql);
}

if (!well_db_find_field("{$tablepre}well_thread", 'mainpic_aid')) {
    $sql = "ALTER TABLE {$tablepre}well_thread ADD COLUMN mainpic_aid int(11) NOT NULL DEFAULT '0';";
    $r = db_exec($sql);
}

if (!well_db_find_field("{$tablepre}well_thread", 'author_id')) {
    $sql = "ALTER TABLE {$tablepre}well_thread ADD COLUMN author_id int(11) NOT NULL DEFAULT '0';";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}well_thread ADD COLUMN source_id int(11) NOT NULL DEFAULT '0';";
    $r = db_exec($sql);
}

if (!well_db_find_field("{$tablepre}forum", 'well_pagesize')) {
    $sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_pagesize int(11) NOT NULL DEFAULT '20';";
    $r = db_exec($sql);
}

if (!well_db_find_table("{$tablepre}well_author")) {
    $sql = "CREATE TABLE {$tablepre}well_author (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` char(32) NOT NULL DEFAULT '' COMMENT '作者',
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);

    $sql = "CREATE TABLE {$tablepre}well_author_thread (
  `author_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`author_id`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

// 2018 03 28
if (!well_db_find_table("{$tablepre}well_source")) {
    $sql = "CREATE TABLE {$tablepre}well_source (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL DEFAULT '' COMMENT '名称',
  `link` char(100) NOT NULL DEFAULT '' COMMENT '链接',
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);

    $sql = "CREATE TABLE {$tablepre}well_source_thread (
  `source_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`source_id`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);
}

// 写入初始配置
$arr = setting_get('well_conf');
if (empty($arr)) {
    $well_conf = array(
        'setting' => array(
            'web_type' => 0,
            'mobile' => 0,
            'register' => 1,
            'single_login' => 0,
            'index' => array(
                'slides' => 10,
                'headline' => 10,
                'guide' => 3,
                'recommend' => 6,
                'tag' => 20,
            ),
        ),
        'mainpic_size' => array(
            'width' => 170,
            'height' => 128,
        ),
        'name' => '',
        'version' => '',
        'installed' => 0,
        'alias' => array(),
        'filter' => array(
            'username' => array(
                'enable' => '0',
                'keyword' => array('admin', ' ', '　', "\t", '管理员', '版主')
            ),
            'content' => array(
                'enable' => '0',
                'keyword' => array('毛泽东', '性服务', '发票'),
                // Pro
                'html_enable' => '0',
                'html_tag' => '&lt;img&gt;&lt;p&gt;&lt;h1&gt;&lt;h2&gt;&lt;h3&gt;&lt;h4&gt;&lt;h5&gt;&lt;h6&gt;&lt;strong&gt;&lt;em&gt;'
            )
        ),
        'forum' => array()
    );
    setting_set('well_conf', $well_conf);
}

?>