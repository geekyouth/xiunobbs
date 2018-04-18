<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$setting_route = param(3, 'list');

// hook well_content_setting_sitemap_start.php

if ($setting_route == 'list') {
    // 设置后台
    $header['title'] = lang('well_sitemap');
    $header['mobile_title'] = lang('well_sitemap');
    $header['mobile_link'] = url('content-setting-sitemap');

    $domain = trim(http_url_path(), '/');
    $dir = 'sitemap/';
    $arr = array();
    foreach (glob($dir.'*.*') as $file) {
        $arr[] = $domain . '/' . $file;
    }

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_sitemap.htm');

} elseif ($setting_route == 'create') {

    $page = param('page', 0) + 1;
    $pagesize = 40000;

    xn_mkdir(APP_PATH . 'sitemap', 0777);

    $fids = arrlist_values($forumlist_show, 'fid');
    $threads = arrlist_sum($forumlist_show, 'threads');
    $fid_n = count($fids);
    // 小数进一
    $n = ceil(($threads + $fid_n) / $pagesize);

    $threadlist = db_find('thread', array('fid' => $fids), array('tid' => 1), $page, $pagesize, '', array('fid', 'tid', 'create_date'));
    if (empty($threadlist)) return array();

    $xml = '';
    $domain = trim(http_url_path(), '/');

    if ($page == 1) {
        foreach ($forumlist_show as $_forum) {
            $xml .= '    <url>
        <loc>' . $domain . '/' . well_nav_url_format($_forum) . '</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>' . "\r\n";
        }
    }

    foreach ($threadlist as $_thread) {
        $xml .= '    <url>
        <loc>' . $domain . '/' . well_url_format($_thread['fid'], $_thread['tid']) . '</loc>
        <lastmod>' . date('Y-m-d', $_thread['create_date']) . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>' . "\r\n";
    }

    $xml = trim($xml, "\r\n");
    $maps = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemapindex>
{$xml}
    </sitemapindex>
</urlset>
EOT;

    file_put_contents_try(APP_PATH . '/sitemap/sitemap_' . $page . '.xml', $maps);

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_setting_sitemap_create.htm');
}

// hook well_content_setting_sitemap_end.php

?>