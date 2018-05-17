<?php exit;
if($notice['is_secret']) {
    $notice['from_username'] = '******';
    $notice['from_user_avatar_url'] = 'view/img/avatar.png';
}