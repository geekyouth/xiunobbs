<?php exit;
// 发表内容过滤关键词
param('message', '', FALSE) AND well_filter_keyword(param('message'), 'content', $error) AND message('message', lang('well_message_contain_keyword') . $error);
?>