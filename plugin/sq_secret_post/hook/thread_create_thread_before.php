<?php exit;
$isSecret = param('is_secret'); // 选中的话是on

$isSecret = $isSecret == 'on' ? 1 : 0;
$thread['is_secret'] = $isSecret;