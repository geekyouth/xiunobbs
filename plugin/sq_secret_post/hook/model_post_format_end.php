<?php exit;
if($post['is_secret']) {
    $post['username'] = '******';
	$post['user_avatar_url'] = 'view/img/avatar.png';
	$post['user'] = $user;
}