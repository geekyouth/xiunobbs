<?php exit;
// 过滤帖子标题 内容 敏感词
well_filter_keyword($subject, 'content', $error) AND message('subject', lang('well_thread_contain_keyword') . $error);

well_filter_keyword($message, 'content', $error) AND message('message', lang('well_message_contain_keyword') . $error);
?>