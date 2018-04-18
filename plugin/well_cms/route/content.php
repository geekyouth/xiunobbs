<?php
/*
* Copyright (C) 2018 wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

user_login_check();

if (DEBUG < 3) {
    // 非正式管理员一律不予进入
    if ($gid > 4 && $gid <= 0) {
        setcookie('bbs_sid', '', $time - 86400);
        http_location(url('user-login'));
    }
    // 有部分用户
    define('XN_ADMIN_BIND_IP', array_value($conf, 'admin_bind_ip'));
    // 管理员令牌检查
    well_admin_token_check();
}

// hook well_content_start.php

// 获取左侧列表数组 频道->子栏目 顶级栏目
$navlist = well_cms_nav_arr();
// 0电脑 1微信 2手机浏览器 3Pad
$detect = well_detect_device();

$installed = array_value($_SERVER['well_conf'], 'installed');

// hook well_content_before.php

$action = param(1, 'default');
$cid = $fid = 0;

if ($action == 'login') {

    if ($method == 'GET') {

        // hook well_content_login_get_start.php

        $header['title'] = lang('admin_login');

        // hook well_content_login_get_end.php

        include _include(APP_PATH . './plugin/well_cms/view/htm/content_login.htm');

    } elseif ($method == 'POST') {

        // hook well_content_login_post_start.php

        $password = param('password');

        if (md5($password . $user['salt']) != $user['password']) {
            xn_log('password error. uid:' . $user['uid'] . ' - ******' . substr($password, -6), 'admin_login_error');
            message('password', lang('password_incorrect'));
        }

        well_admin_token_set();

        xn_log('login successed. uid:' . $user['uid'], 'admin_login');

        // hook well_content_login_post_end.php

        message(0, jump(lang('login_successfully'), url('content-default')));
    }

} elseif ($action == 'logout') {

    // hook well_content_logout_start.php

    well_admin_token_clean();

    // hook well_content_logout_end.php

    message(0, jump(lang('logout_successfully'), './'));

} elseif ($action == 'default') {

    $header['title'] = lang('well_content_index') . '-' . $conf['sitename'];
    $header['mobile_title'] = lang('well_content_index');
    $header['mobile_link'] = url('content-default');
    $header['keywords'] = '';
    $header['description'] = lang('well_content_index');
    $soft = well_software_info();

    // hook well_content_default_start.php

    $cmslist = well_get_cms_list($forumlist);
    $channellist = well_get_cms_channellist($forumlist);
    $channel_num = count($channellist);
    $forum_num = count($cmslist) - $channel_num;

    // hook well_content_default_end.php

    include _include(APP_PATH . './plugin/well_cms/view/htm/content_default.htm');

} elseif ($action == 'setting') {

    $setting_action = param(2, 'system');

    // hook well_content_setting_start.php

    switch ($setting_action) {
        // hook well_content_setting_route_case_start.php
        case 'system':
            include _include(APP_PATH . './plugin/well_cms/route/content_system.php');
            break;
        case 'column':
            include _include(APP_PATH . './plugin/well_cms/route/content_column.php');
            break;
        case 'filter':
            include _include(APP_PATH . './plugin/well_cms/route/content_filter.php');
            break;
        case 'author':
            include _include(APP_PATH . './plugin/well_cms/route/content_author.php');
            break;
        case 'source':
            include _include(APP_PATH . './plugin/well_cms/route/content_source.php');
            break;
        case 'sitemap':
            include _include(APP_PATH . './plugin/well_cms/route/content_sitemap.php');
            break;
        // hook well_content_setting_route_case_end.php
        default:
            include _include(APP_PATH . './plugin/well_cms/route/content.php');
            break;
    }

    // hook well_content_setting_end.php

    exit();

} elseif ($action == 'list') {

    // hook well_content_list_start.php

    $fid = param(2) ? param(2) : param('fid', 0);// 方便前台直进入编辑
    $forum = forum_read($fid);
    empty($forum) AND message(-1, lang('forum_not_exists'));

    $header['title'] = $forum['seo_title'] ? $forum['seo_title'] : $forum['name'] . '-' . $conf['sitename'];
    $header['mobile_title'] = $forum['name'];
    $header['mobile_link'] = url("content-list-$fid-$cid");
    $header['keywords'] = '';
    $header['description'] = $forum['brief'];
    well_set_cookie_forumarr('CMS');
    // hook well_content_list_before.php

    $cid = param(3, 0); // 分类ID
    $page = param(4, 1);
    $pagesize = 30;

    $threadlist = thread_find_by_fid($fid, $page, $pagesize);

    $pagination = pagination(url('content-list-' . $fid . '-' . $cid . '-{page}'), $forum['threads'], $page, $pagesize);

    // hook well_content_list_after.php

    $menulist = well_get_cms_forumlist($forumlist);
    $forumarr = arrlist_key_values($menulist, 'fid', 'name');

    // hook well_content_list_end.php

    if ($ajax) {
        $forum = forum_safe_info($forum);
        foreach ($threadlist as &$thread) $thread = thread_safe_info($thread);
        message(0, array('forum' => $forum, 'threadlist' => $threadlist));
    } else {
        include _include(APP_PATH . './plugin/well_cms/view/htm/content_list.htm');
    }

} elseif ($action == 'create') {

    // hook well_content_create_get_post.php

    if ($method == 'GET') {

        // hook well_content_create_get_start.php

        $fid = param(2);
        $forum = forum_read($fid);
        if (empty($forum)) message(-1, lang('user_group_insufficient_privilege'));

        $cid = param(3, 0);

        $header['title'] = lang('thread_create');
        $header['mobile_title'] = $forum['name'];
        $header['mobile_link'] = url("content-create");

        // hook well_content_create_get_before.php

        // 获取主图
        $nopic = './plugin/well_cms/view/image/nopic.png';
        $mainpic = array('1' => $nopic, '2' => $nopic, '3' => $nopic, '4' => $nopic);
        $filepic = _SESSION('tmp_mainpic');
        if (!empty($filepic)) {
            foreach ($filepic as $key => $file) {
                if ($file) {
                    $mainpic[$key] = $file['url'];
                }
            }
        }

        $author_arr = well_admin_authors_key_values();
        $source_arr = well_admin_sources_key_values();

        // hook well_content_create_get_center.php

        $flag_arr = array('1' => lang('well_slides'), '2' => lang('well_headline'), '3' => lang('well_guide'), '4' => lang('well_recommend'));

        // hook well_content_create_flag_checkbox.php

        $flag = well_form_multi_checkbox('flag', $flag_arr, array());

        $well_conf = $_SERVER['well_conf']['mainpic_size'];
        $pic_width = $well_conf['width'];
        $pic_height = $well_conf['height'];

        // hook well_content_create_get_end.php

        include _include(APP_PATH . './plugin/well_cms/view/htm/content_post.htm');

    } elseif ($method == 'POST') {

        // hook well_content_create_post_start.php

        $fid = param('fid', 0);
        $forum = forum_read($fid);
        empty($forum) AND message('fid', lang('forum_not_exists'));

        $r = forum_access_user($fid, $gid, 'allowthread');
        !$r AND message(-1, lang('user_group_insufficient_privilege'));

        $subject = htmlspecialchars(param('subject', '', FALSE));
        empty($subject) AND message('subject', lang('please_input_subject'));
        xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength' => 128)));
        // 过滤帖子标题 关键词
        well_filter_keyword($subject, 'content', $error) AND message('subject', lang('well_thread_contain_keyword') . $error);

        $brief = htmlspecialchars(param('brief', '', FALSE));
        //xn_strlen($brief) > 128 AND message('brief', lang('well_thread_brief_length_over_limit', array('maxlength' => 128)));
        xn_strlen($brief) > 120 AND $brief = xn_substr($brief, 0, 120);
        // 过滤帖子简介 关键词
        $brief AND well_filter_keyword($brief, 'content', $error) AND message('brief', lang('well_brief_contain_keyword') . $error);

        $message = param('message', '', FALSE);
        empty($message) AND message('message', lang('please_input_message'));
        // 过滤帖子 内容 关键词
        well_filter_keyword($message, 'content', $error) AND message('message', lang('well_message_contain_keyword') . $error);

        // hook well_content_create_post_message.php

        $doctype = param('doctype', 0);
        $doctype > 10 AND message(-1, lang('doc_type_not_supported'));
        xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));

        // hook well_content_create_post_before.php

        $flag = param('flag', array());
        $flags = '';
        $flagarr = array();
        // 主题属性入库主题附表
        if ($flag) {
            foreach ($flag as $k => $v) {
                if ($v) {
                    $flags .= $k . ',';
                    $flagarr[] = $k;
                }
            }
            $flags = trim($flags, ',');
        }

        $tag = param('tag', '', FALSE);
        // 过滤帖子标签 关键词
        $tag AND well_filter_keyword($tag, 'content', $error) AND message('tag', lang('well_tag_contain_keyword') . $error);

        // 作者
        $author_id = param('author_id', 0);
        $author_name = param('author_name', '', TRUE, TRUE);
        $author_id = well_cms_author_id($author_id, $author_name);

        // 来源
        $source_id = param('source_id', 0);
        $source_name = param('source_name', '', TRUE, TRUE);
        $source_link = param('source_link', '', TRUE, TRUE);
        $source_id = well_cms_source_id($source_id, $source_name, $source_link);

        $thread = array('fid' => $fid, 'uid' => $uid, 'sid' => $sid, 'subject' => $subject, 'message' => $message, 'time' => $time, 'longip' => $longip, 'doctype' => $doctype, 'brief' => $brief, 'tag' => $tag, 'flag' => $flags, 'flagarr' => $flagarr, 'author_id' => $author_id, 'source_id' => $source_id, 'admin' => 1);

        // hook well_content_create_post_after.php

        $tid = well_cms_thread_create($thread, $pid);
        $pid === FALSE AND message(-1, lang('create_post_failed'));
        $tid === FALSE AND message(-1, lang('create_thread_failed'));

        // 创建作者主题
        if ($tid && $author_id !== FALSE) {
            well_cms_author_thread_create(array('author_id' => $author_id, 'tid' => $tid));
        }

        // 创建来源主题
        if ($tid && $source_id !== FALSE) {
            well_cms_source_thread_create(array('source_id' => $source_id, 'tid' => $tid));
        }

        // hook well_content_create_post_tag.php

        // 写入主题属性标记表
        if ($flag) {
            foreach ($flag as $k => $v) {
                $v AND well_cms_thread_flag_create(array('fid' => $fid, 'fup' => $forum['well_fup'], 'tid' => $tid, 'flag' => $k));
            }
        }

        well_cms_delete_cache();

        // hook well_content_create_post_end.php

        message(0, lang('create_thread_sucessfully'));
    }

} elseif ($action == 'update') {

    $pid = param(2);
    $post = post_read($pid);
    empty($post) AND message(-1, lang('post_not_exists'));

    $tid = $post['tid'];
    $thread = thread_read($tid);
    empty($thread) AND message(-1, lang('thread_not_exists'));

    // 主题附表
    $well_thread = well_cms_thread_read($tid);
    empty($well_thread) AND message(-1, lang('thread_not_exists'));

    $fid = $thread['fid'];
    $forum = forum_read($fid);
    empty($forum) AND message(-1, lang('forum_not_exists'));

    $cid = param(3, 0);

    $isfirst = $post['isfirst'];

    !forum_access_user($fid, $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));
    $allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
    !$allowupdate AND !$post['allowupdate'] AND message(-1, lang('have_no_privilege_to_update'));
    !$allowupdate AND $thread['closed'] AND message(-1, lang('thread_has_already_closed'));

    // hook well_content_update_get_post.php

    if ($method == 'GET') {

        $header['title'] = lang('post_update');
        $header['mobile_title'] = $forum['name'];
        $header['mobile_link'] = url("content-update-$pid-$cid");

        // hook well_content_update_get_start.php

        $mainpic = array('1' => well_mainpic($well_thread));

        // hook well_content_update_main.php

        $well_conf = $_SERVER['well_conf']['mainpic_size'];
        $pic_width = $well_conf['width'];
        $pic_height = $well_conf['height'];

        // hook well_content_update_main_size.php

        $flag_arr = array('1' => lang('well_slides'), '2' => lang('well_headline'), '3' => lang('well_guide'), '4' => lang('well_recommend'));

        // hook well_content_update_flag_checkbox.php

        $flag = well_form_multi_checkbox('flag', $flag_arr, explode(',', $well_thread['flag']));

        $author_arr = well_admin_authors_key_values();
        $source_arr = well_admin_sources_key_values();

        // 如果为数据库减肥，则 message 可能会被设置为空
        $post['message'] = htmlspecialchars($post['message'] ? $post['message'] : $post['message_fmt']);

        $attachlist = $imagelist = $filelist = array();
        if ($post['files']) {
            list($attachlist, $imagelist, $filelist) = attach_find_by_pid($pid);
        }

        $tags = '';
        if (!empty($well_thread['tag'])) {
            $arr = xn_json_decode($well_thread['tag']);
            if (!empty($arr)) {
                foreach ($arr as $v) $tags .= $v . ' ';
                $tags = trim($tags);
            }
        }

        // hook well_content_update_get_end.php

        include _include(APP_PATH . './plugin/well_cms/view/htm/content_post.htm');

    } elseif ($method == 'POST') {

        // hook well_content_post_update_post_start.php

        $subject = htmlspecialchars(param('subject', '', FALSE));
        empty($subject) AND message('subject', lang('please_input_subject'));
        xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength' => 128)));
        // 过滤帖子标题 关键词
        well_filter_keyword($subject, 'content', $error) AND message('subject', lang('well_thread_contain_keyword') . $error);

        $message = param('message', '', FALSE);
        empty($message) AND message('message', lang('please_input_message'));
        mb_strlen($message, 'UTF-8') > 2048000 AND message('message', lang('message_too_long'));
        // 过滤帖子 内容 关键词
        well_filter_keyword($message, 'content', $error) AND message('message', lang('well_message_contain_keyword') . $error);

        // hook well_content_post_update_post_message.php

        $doctype = param('doctype', 0);

        $brief = htmlspecialchars(param('brief', '', FALSE));
        //xn_strlen($brief) > 128 AND message('brief', lang('well_thread_brief_length_over_limit', array('maxlength' => 128)));
        xn_strlen($brief) > 120 AND $brief = xn_substr($brief, 0, 120);
        // 过滤帖子简介 关键词
        $brief AND well_filter_keyword($brief, 'content', $error) AND message('brief', lang('well_brief_contain_keyword') . $error);

        // hook well_content_update_post_before.php

        $arr = array();
        $newfid = param('fid');
        $forum = forum_read($newfid);
        empty($forum) AND message('fid', lang('forum_not_exists:'));

        if ($fid != $newfid) {
            !forum_access_user($fid, $gid, 'allowthread') AND message(-1, lang('user_group_insufficient_privilege'));
            $post['uid'] != $uid AND !forum_access_mod($fid, $gid, 'allowupdate') AND message(-1, lang('user_group_insufficient_privilege'));
            $arr['fid'] = $newfid;
        }
        if ($subject != $thread['subject']) {
            mb_strlen($subject, 'UTF-8') > 80 AND message('subject', lang('subject_max_length', array('max' => 80)));
            $arr['subject'] = $subject;
        }

        // hook well_content_update_post_thread.php

        $arr AND thread_update($tid, $arr) === FALSE AND message(-1, lang('update_thread_failed'));

        // hook well_content_update_post_thread_before.php

        // 更新主题属性
        $update_well_thread = array();
        if ($brief != $well_thread['brief']) {
            xn_strlen($brief) > 128 AND $brief = xn_substr($brief, 0, 128);
            $update_well_thread['brief'] = $brief;
        }

        $flag = param('flag', array());
        $flags = '';
        // 更新主题属性附表
        if ($flag) {
            foreach ($flag as $k => $v) {
                $v AND $flags .= $k . ',';
            }
            $flags = rtrim($flags, ',');
        }

        if ($flags != $well_thread['flag']) {
            $update_well_thread['flag'] = $flags;

            // 清理主题属性
            well_cms_thread_flag_delete_old($fid, $forum['well_fup'], $tid, $well_thread['flag'], $flags) === FALSE AND message(-1, lang('update_thread_failed'));
        }

        // 作者
        $author_id = param('author_id', 0);
        $author_name = param('author_name', '', TRUE, TRUE);
        if ($author_id != $well_thread['author_id'] || $author_name) {
            $author_id = well_cms_author_id_update($author_id, $well_thread['author_id'], $author_name);
            $author_id !== FALSE AND $update_well_thread['author_id'] = $author_id;
            // 之前为空创建
            !$well_thread['author_id'] AND well_cms_author_thread_create(array('author_id' => $author_id, 'tid' => $tid));
        }

        // 来源
        $source_id = param('source_id', 0);
        $source_name = param('source_name', '', TRUE, TRUE);
        $source_link = param('source_link', '', TRUE, TRUE);
        if ($source_id != $well_thread['source_id'] || $source_name) {
            $source_id = well_cms_source_id_update($source_id, $well_thread['source_id'], $source_name, $source_link);
            $source_id !== FALSE AND $update_well_thread['source_id'] = $source_id;
            // 之前为空创建
            !$well_thread['source_id'] AND well_cms_source_thread_create(array('source_id' => $source_id, 'tid' => $tid));
        }

        $tag = param('tag', '', FALSE);
        // 过滤帖子标签 关键词
        $tag AND well_filter_keyword($tag, 'content', $error) AND message('tag', lang('well_tag_contain_keyword') . $error);
        $tags = '';
        $tags = well_cms_post_tag_update($tid, $tag, $well_thread['tag']);

        if ($tags) {
            if ($tags != $well_thread['tag']) {
                $update_well_thread['tag'] = $tags;
            }
        } else {
            $update_well_thread['tag'] = '';
        }

        // hook well_content_update_post_well_thread.php
        // 关联主图
        $attach = well_cms_attach_assoc($pid, $tid);
        if (!empty($attach)) {
            !empty($attach) AND $update_well_thread['mainpic_aid'] = $attach[1]['aid'];
            !empty($attach) AND $update_well_thread['mainpic'] = $attach[1]['picture'];

            // 删除旧主图
            $well_thread['mainpic_aid'] AND attach__delete($well_thread['mainpic_aid']);
        }

        $r = well_cms_thread_update($tid, $update_well_thread);

        $r = well_cms_post_update($post, array('doctype' => $doctype, 'message' => $message));
        $r === FALSE AND message(-1, lang('update_post_failed'));

        well_cms_delete_cache();

        // hook well_content_update_post_end.php

        message(0, lang('update_successfully'));

    }

} elseif ($action == 'attach') {

    // 上传主图或预览图
    $assoc = param('assoc', '', FALSE);
    $width = param('width', 0);
    $height = param('height', 0);
    $is_image = param('is_image', 0);
    $name = param('name');
    $data = param('data', '', FALSE, FALSE);
    $n = intval(xn_substr($assoc, 4, 1));

    // hook well_content_attach_create_start.php

    empty($group['allowattach']) AND $gid != 1 AND message(-1, '您无权上传');

    empty($data) AND message(-1, lang('data_is_empty'));
    $data = base64_decode_file_data($data);
    $size = strlen($data);
    $size > 20480000 AND message(-1, lang('filesize_too_large', array('maxsize' => '20M', 'size' => $size)));

    $ext = file_ext($name, 7);
    $filetypes = include APP_PATH . 'conf/attach.conf.php';
    !in_array($ext, $filetypes['image']) AND $ext = '_' . $ext;

    $tmpanme = 'main_' . $uid . '_' . xn_rand(15) . '.' . $ext;
    $tmpfile = $conf['upload_path'] . 'tmp/' . $tmpanme;
    $tmpurl = $conf['upload_url'] . 'tmp/' . $tmpanme;

    $filetype = attach_type($name, $filetypes);

    // hook well_content_attach_create_save_before.php

    file_put_contents($tmpfile, $data) OR message(-1, lang('write_to_file_failed'));

    // 保存到 session，发帖成功以后，关联到帖子。
    // 抛弃之前的 $_SESSION 数据，重新启动 session，降低 session 并发写入的问题
    sess_restart();

    $filesize = filesize($tmpfile);
    $attach = array(
        'url' => $tmpurl,
        'path' => $tmpfile,
        'orgfilename' => $name,
        'filetype' => $filetype,
        'filesize' => $filesize,
        'width' => $width,
        'height' => $height,
        'isimage' => $is_image,
        'aid' => '_' . $n
    );

    // hook well_content_attach_create_save_after.php

    $_SESSION['tmp_mainpic'][$n] = $attach;

    unset($attach['path']);

    // hook well_content_attach_create_end.php

    message(0, $attach);
} elseif ($action == 'tag') {
    // tag
    if ($method == 'GET') {

        $header['title'] = lang('well_tag_manage');
        $header['mobile_title'] = lang('well_tag_manage');
        $header['mobile_link'] = url("content-tag");
        $page = param(2, 1);
        $pagesize = 36;

        $arr = well_cms_tag_count_desc__find($page, $pagesize);
        $count = well_cms_tag__count();

        $pagination = pagination(url('content-tag-{page}'), $count, $page, $pagesize);

        if ($ajax) {
            $arr = well_cms_tag_safe_info($arr);
            message(0, array('taglist' => $arr));
        } else {
            include _include(APP_PATH . './plugin/well_cms/view/htm/content_tag_list.htm');
        }
    } elseif ($method == 'POST') {

        $tagid = param('tagid', 0);

        if ($tagid) {
            $r = well_cms_tag_delete($tagid);
            $r === FALSE AND message(-1, lang('delete_failed'));

            message(0, lang('delete_successfully'));
        }
    }
} elseif ($action == 'readtag') {

    $tagid = param(2, 0);
    $page = param(3, 1);
    $pagesize = 20;

    $header['title'] = lang('well_tag_manage');
    $header['mobile_title'] = lang('well_tag_manage');
    $header['mobile_link'] = url("content-readtag-$tagid");

    $r = well_cms_tag_read_tagid($tagid);

    $arr = well_cms_tag_data_find($tagid, $page, $pagesize);
    $tids = arrlist_values($arr, 'tid');
    $count = well_cms_tag_data_tagid_count($tagid);

    if ($count) {
        $threadlist = well_cms_tag_data_thread_find($tids, 1, $pagesize);
    }

    $pagination = pagination(url('content-readtag-' . $tagid . '-{page}'), $count, $page, $pagesize);

    if ($ajax) {
        foreach ($threadlist as &$thread) $thread = thread_safe_info($thread);
        message(0, array('threadlist' => $threadlist));
    } else {
        include _include(APP_PATH . './plugin/well_cms/view/htm/content_tag_read_list.htm');
    }
} elseif ($action == 'demo') {

    // 仅管理员可以安装卸载演示数据
    if ($gid == 1) {

        $type = param(2, '', FALSE);

        if ($type == 'install') {

            if ($runtime['threads']) {
                message(0, jump(lang('well_install_error'), url('content-default'), 2));
            }

            well_install_demo();
            cache_truncate();
            $runtime = NULL;
            message(0, jump(lang('well_install_success'), url('content-default'), 2));
        } elseif ($type == 'unstall') {
            well_unstall_demo();
            cache_truncate();
            $runtime = NULL;
            message(0, jump(lang('well_unstall_success'), url('content-default'), 2));
        }
    } else {
        message(0, jump(lang('user_group_insufficient_privilege'), url('content-default'), 2));
    }
} elseif ($action == 'license') {
    $header['title'] = lang('well_license');
    $header['mobile_title'] = lang('well_license');
    $header['mobile_link'] = url("content-license");
    include _include(APP_PATH . './plugin/well_cms/view/htm/content_license.htm');
}

// hook well_content_end.php

function well_admin_token_check()
{
    $conf = $_SERVER['conf'];
    $time = $_SERVER['time'];
    $useragent = $_SERVER['useragent'];
    $longip = $_SERVER['longip'];

    $useragent_md5 = md5($useragent);

    //$key = md5($longip.$useragent_md5.$conf['auth_key']); // 有些环境是动态 IP
    $key = md5((XN_ADMIN_BIND_IP ? $longip : '') . $useragent_md5 . xn_key());

    // hook well_content_token_check_start.php

    $admin_token = param('wellcms_admin_token');
    if (empty($admin_token)) {
        $_REQUEST[0] = 'content';
        $_REQUEST[1] = 'login';
    } else {
        $s = xn_decrypt($admin_token, $key);
        if (empty($s)) {
            setcookie('wellcms_admin_token', '', 0, '', '', '', TRUE);
            //message(-1, lang('admin_token_error'));
            message(-1, lang('admin_token_expiry'));
        }
        list($_ip, $_time) = explode("\t", $s);

        // 后台超过 3600 自动退出。
        // Background / more than 3600 automatic withdrawal.
        //if($_ip != $longip || $time - $_time > 3600) {
        if ((XN_ADMIN_BIND_IP && $_ip != $longip || !XN_ADMIN_BIND_IP) && $time - $_time > 3600) {
            setcookie('wellcms_admin_token', '', 0, '', '', '', TRUE);
            message(-1, lang('admin_token_expiry'));
        }

        // 超过半小时，重新发新令牌，防止过期
        // More than half an hour, reset a new token, prevent expired
        if ($time - $_time > 1800) {
            well_admin_token_set();
        }
    }
    // hook well_content_token_check_end.php
}

function well_admin_token_set()
{
    $conf = $_SERVER['conf'];
    $time = $_SERVER['time'];
    $useragent = $_SERVER['useragent'];
    $longip = $_SERVER['longip'];

    $useragent_md5 = md5($useragent);
    //$key = md5($longip.$useragent_md5.$conf['auth_key']);
    $key = md5((XN_ADMIN_BIND_IP ? $longip : '') . $useragent_md5 . xn_key());

    // hook well_content_token_set_start.php

    $admin_token = param('wellcms_admin_token');
    $s = "$longip	$time";

    $admin_token = xn_encrypt($s, $key);
    setcookie('wellcms_admin_token', $admin_token, $time + 3600, '', '', 0, TRUE);

    // hook well_content_token_set_end.php
}

function well_admin_token_clean()
{
    $conf = $_SERVER['conf'];
    $time = $_SERVER['time'];

    setcookie('wellcms_admin_token', '', $time - 86400, '', '', 0, TRUE);

    // hook well_content_token_clean_start.php
}

?>