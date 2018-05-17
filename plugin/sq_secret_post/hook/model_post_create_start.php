<?php exit;
$isSecret = param('is_secret');
$isSecret = $isSecret == 'on' ? 1 : 0;

$arr['is_secret'] = $isSecret;