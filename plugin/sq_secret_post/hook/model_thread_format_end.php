<?php exit;

if($thread['is_secret']) {
    $thread['username'] = '******';
	$thread['user_avatar_url'] = 'view/img/avatar.png';
	$thread['user'] = $user;
}
