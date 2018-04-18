// 处理postlist里面的message_fmt
foreach($postlist as &$item) {
	// 下面这个图像的边框设为0
	$item['message_fmt'] = preg_replace ( "[\[em_([0-9]*)\]]", "<img src=\"http://localhost/xiunobbs/plugin/sq_qqface/static/img/$1.gif\" style=\"border: 0px;\" />", $item['message_fmt'] );
}