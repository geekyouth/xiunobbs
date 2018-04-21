<?php

// ------------> 最原生的 CURD，无关联其他数据。



function modlog__create($arr) {
	
	$r = db_create('modlog', $arr);
	
	return $r;
}

function modlog__update($logid, $arr) {
	
	$r = db_update('modlog', array('logid'=>$logid), $arr);
	
	return $r;
}

function modlog__read($logid) {
	
	$modlog = db_find_one('modlog', array('logid'=>$logid));
	
	return $modlog;
}

function modlog__delete($logid) {
	
	$r = db_delete('modlog', array('logid'=>$logid));
	
	return $r;
}

function modlog__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20) {
	
	$modloglist = db_find('modlog', $cond, $orderby, $page, $pagesize);
	
	return $modloglist;
}

// ------------> 关联 CURD，主要是强相关的数据，比如缓存。弱相关的大量数据需要另外处理。

function modlog_create($arr) {
	
	$r = modlog__create($arr);
	
	return $r;
}

function modlog_update($logid, $arr) {
	
	$r = modlog__update($logid, $arr);
	
	return $r;
}

function modlog_read($logid) {
	
	$modlog = modlog__read($logid);
	$modlog AND modlog_format($modlog);
	
	return $modlog;
}

function modlog_delete($logid) {
	
	$r = modlog__delete($logid);
	
	return $r;
}

function modlog_find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20) {
	
	$modloglist = modlog__find($cond, $orderby, $page, $pagesize);
	if($modloglist) foreach ($modloglist as &$modlog) modlog_format($modlog);
	
	return $modloglist;
}

// ----------------> 其他方法

function modlog_format(&$modlog) {
	
	global $conf;
	$modlog['create_date_fmt'] = date('Y-n-j', $modlog['create_date']);
	
}

function modlog_count($cond = array()) {
	
	$n = db_count('modlog', $cond);
	
	return $n;
}

function modlog_maxid() {
	
	$n = db_maxid('modlog', 'logid');
	
	return $n;
}



?>