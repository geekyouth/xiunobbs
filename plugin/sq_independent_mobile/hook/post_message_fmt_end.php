    // 对引用进行处理
    !empty($arr['quotepid']) && $arr['quotepid'] > 0 && $arr['message_fmt'] = $arr['message_fmt'] . post_quote($arr['quotepid']);
    return;