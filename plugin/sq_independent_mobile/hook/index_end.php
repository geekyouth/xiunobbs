// 处理置顶，重新排序数组
// sort($threadlist);

if(IS_MOBILE) {
	$is_index = 1; // 用来显示搜索框
	include(APP_PATH . SQ_MOBILE_PATH . '/view/htm/index.htm');
	exit();
}