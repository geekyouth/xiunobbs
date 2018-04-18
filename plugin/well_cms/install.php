<?php
/*
 * Copyright (C) 燃烧的冰 81340116@qq.com
 */

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

// 2018 03 24
$sql = "ALTER TABLE {$tablepre}user ADD COLUMN well_token char(60) NOT NULL default '';";
$r = db_exec($sql);

# 版块增加well_nav_display栏目是否显示在导航
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_nav_display tinyint(1) NOT NULL DEFAULT '0' COMMENT '1显示'";;
$r = db_exec($sql);

# well_display 首页是否显示内容
# 版块内容 首页是否显示内容 1显示
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_display tinyint(1) NOT NULL DEFAULT '0' COMMENT '1显示'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_news int(11) NOT NULL DEFAULT '0' COMMENT '首页显示数量'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_headlines int(11) NOT NULL DEFAULT '0' COMMENT '首页头条数量'";
$r = db_exec($sql);

# 设置为频道 显示数量
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_slides int(11) NOT NULL DEFAULT '0' COMMENT '频道轮播显示数'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_headline int(11) NOT NULL DEFAULT '0' COMMENT '频道头条显示数'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_guide int(11) NOT NULL DEFAULT '0' COMMENT '频道导读显示数'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_recommend int(11) NOT NULL DEFAULT '0' COMMENT '频道推荐显示数'";
$r = db_exec($sql);

# 版块增加well_comment评论开启
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_comment tinyint(1) NOT NULL DEFAULT '0' COMMENT '评论1开启'";
$r = db_exec($sql);

# 设置为列表
# 频道是否显示内容
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_list_news int(11) NOT NULL DEFAULT '0' COMMENT '列表最新数量'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_list_headlines int(11) NOT NULL DEFAULT '0' COMMENT '列表头条数量'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_list_recommends int(11) NOT NULL DEFAULT '0' COMMENT '列表推荐数量'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_display tinyint(1) NOT NULL DEFAULT '0' COMMENT '1显示'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_news int(11) NOT NULL DEFAULT '0' COMMENT '频道最新数量'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_channel_headlines int(11) NOT NULL DEFAULT '0' COMMENT '频道头条数量'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_fup int(11) NOT NULL DEFAULT '0' COMMENT '频道ID'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_son int(11) NOT NULL DEFAULT '0' COMMENT '子栏目数'";
$r = db_exec($sql);

# 分类类型 (0论坛 1cms)
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_type tinyint(1) NOT NULL DEFAULT '0' COMMENT '0论坛 1cms'";
$r = db_exec($sql);

# 模型 0新闻
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_model tinyint(1) NOT NULL DEFAULT '0' COMMENT '模型'";
$r = db_exec($sql);

# 版块属性 (0列表 1频道)
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_forum_type tinyint(1) NOT NULL DEFAULT '0' COMMENT '0列表 1频道'";
$r = db_exec($sql);

# 别名路径 暂时未用到 #####
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_alias char(30) NOT NULL DEFAULT ''";
$r = db_exec($sql);

# 模板
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_tpl tinyint(1) NOT NULL DEFAULT '0' COMMENT '模板0默认 1自建'";
$r = db_exec($sql);

# 分类页模板
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_tpl_class char(30) NOT NULL DEFAULT '' COMMENT '分类页模板'";
$r = db_exec($sql);

# 内容页模板
$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_tpl_show char(30) NOT NULL DEFAULT '' COMMENT '内容页模板'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_count_slides int(11) NOT NULL DEFAULT '0' COMMENT '轮播统计'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_count_headline int(11) NOT NULL DEFAULT '0' COMMENT '头条统计'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_count_guide int(11) NOT NULL DEFAULT '0' COMMENT '导读统计'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_count_recommend int(11) NOT NULL DEFAULT '0' COMMENT '推荐统计'";
$r = db_exec($sql);

$sql = "ALTER TABLE {$tablepre}forum ADD COLUMN well_pagesize int(11) NOT NULL DEFAULT '20' COMMENT '分页'";
$r = db_exec($sql);

# cms主题附表
$sql = "CREATE TABLE {$tablepre}well_thread (
  `tid` int(11) NOT NULL DEFAULT '0',
  `author_id` int(11) NOT NULL DEFAULT '0', # 作者ID 非UID
  `source_id` int(11) NOT NULL DEFAULT '0', # 来源ID
  `tag` char(180) NOT NULL DEFAULT '',      # 标签 json
  `flag` char(20) NOT NULL DEFAULT '' COMMENT '属性',
  `mainpic_aid` int(11) NOT NULL DEFAULT '0', # 主图附件aid
  `mainpic` char(120) NOT NULL DEFAULT '' COMMENT '主图',
  `brief` char(120) NOT NULL DEFAULT '' COMMENT '摘要',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

// 主题属性 1轮播 2头条 3导读 4推荐
$sql = "CREATE TABLE {$tablepre}well_thread_flag (
  `fid` int(11) NOT NULL DEFAULT '0',  # 版块
  `fup` int(11) NOT NULL DEFAULT '0',  # 频道
  `tid` int(11) NOT NULL DEFAULT '0',  # 主题
  `flag` tinyint(1) NOT NULL DEFAULT '0',  # 属性
  PRIMARY KEY (`flag`,`fid`,`tid`), # 某属性 版块下 主题排序
  KEY `flag_fup_tid` (`flag`,`fup`,`tid`), # 某属性 频道下 主题排序
  KEY `flag_tid` (`flag`,`tid`), # 某属性 主题排序
  KEY `tid` (`tid`)  # 主题所有属性
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE {$tablepre}well_tag (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '',
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`),
  KEY `name` (`name`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE {$tablepre}well_tag_data (
  `tagid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 作者表 # 投稿绑定uid user表绑定author_id
# 后台发布内容不绑定UID 站内编辑是否要绑定uid？
$sql = "CREATE TABLE {$tablepre}well_author (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` char(32) NOT NULL DEFAULT '' COMMENT '作者',
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),  # 唯一
  KEY `name` (`name`) # 后台根据输入的作者名搜索是否存在+1
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 作者主题 后台发布内容不绑定UID
$sql = "CREATE TABLE {$tablepre}well_author_thread (
  `author_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`author_id`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 信息来源表
$sql = "CREATE TABLE {$tablepre}well_source (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) NOT NULL DEFAULT '' COMMENT '名称',
  `link` char(100) NOT NULL DEFAULT '' COMMENT '链接',
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 来源主题 后台发布内容不绑定UID
$sql = "CREATE TABLE {$tablepre}well_source_thread (
  `source_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`source_id`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

// 写入初始配置
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
    // 新加 升级文件需要加入
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
    // 版块其他设置参数 fid => array();
    'forum' => array()
);
setting_set('well_conf', $well_conf);

xn_copy('../plugin/well_cms/view/image/logo.png', '../view/img/well_logo.png');
clearstatcache();

// 初始化
$r === false AND message(-1, '创建数据表结构失败');

?>