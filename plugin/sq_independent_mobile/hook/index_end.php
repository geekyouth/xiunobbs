// 处理置顶，重新排序数组
// sort($threadlist);

$is_index = 1; // 主页
if(IS_MOBILE) {
	foreach($toplist3 as $key => $value) {
		unset($threadlist[$key]); // 将贴列表里面置顶的贴删除，以免重复，那么这个threadlist就是新贴
	} 

	$show_search = 1; // 用来显示搜索框
	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/index.htm');
	return;
} else {
	if($digest == 1) $is_index = 0; // 如果是精华帖页，不显示那3个框

	foreach($toplist3 as $key => $value) {
		unset($threadlist[$key]); // 将贴列表里面置顶的贴删除，以免重复，那么这个threadlist就是新贴
	} 
	
	$lastThread = thread_find_by_fids($fids, $page, 3, 'tid', $threads); // 查找最新的10张贴
	foreach($lastThread as $k => $v) {
		unset($threadlist[$k]); // 去重
	}
	
	include _include(APP_PATH . SQ_MOBILE_PATH . '/view/pc_htm/index.htm'); // PC端首页
	return;
}