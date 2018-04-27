<?php
/**
 * 钉钉免登设置，需要在插件目录下的config配置信息
 */
!defined('DEBUG') AND exit('Access Denied.');

require_once(APP_PATH . "plugin/sq_dd_autoLogin/vendor/nategood/httpful/bootstrap.php"); // 阿里的接口访问类
require_once(APP_PATH . 'plugin/sq_dd_autoLogin/vendor/image/Image.php'); // 图片处理类

include APP_PATH . 'plugin/sq_dd_autoLogin/model/dd_login.func.php'; // 直接读取本插件的公共方法
$ddConfig = include APP_PATH . 'plugin/sq_dd_autoLogin/config.php'; // 插件配置

// 先获得用户的钉钉user_id(前端部分工作)

// 根据这个user_id获得用户的信息
$ddUserId = param('user_id');
// var_dump($ddUserId); die;
// $ddUserId = '1546114653-2075071073'; // 测试 cass

if (!$ddUserId) sq_message(10000, '非法访问'); // 当钉钉id没有传值的时候

/** 如果ddUserId在数据库已经存在的话，则直接调用登陆 */
$ddUser = dd_user_read($ddUserId);

if ($ddUser) {
	// squid 
	$_SESSION['uid'] = $ddUser['uid'];
	user_token_set($ddUser['uid']);
	user_update($ddUser['uid'], array('login_ip' => $longip, 'login_date' => $time , 'logins+' => 1)); // 更新用户的登陆信息和次数
	sq_message(0, lang('user_login_successfully'), [], 1, './?my.htm');
	// http_location('http://loc.xiunobbs.com/');
} else {
	
	/** 成功逻辑 */
	// 获得access_token的url gettoken?corpid=dingde55314a8e20f3f6&corpsecret=L0k5CtQscvusMrbC4aLoVUv_W7VHC9AnYGH0roxD3w6dFlB8DNquukbYMcstqZ2R
	$getTokenData = ['corpid' => $ddConfig['CORPID'], 'corpsecret' => $ddConfig['SECRET']];
	$getTokenUrl = $ddConfig['OAPI_HOST'] . '/gettoken?' . http_build_query($getTokenData);
	
	$tokenResponse = \Httpful\Request::get($getTokenUrl)->send();
	if ($tokenResponse->hasErrors() || $tokenResponse->body->errcode != 0) {
		sq_message(10001, '请求错误，错误码:10001'); // 获取access_token错误
	}
	
	// 获得用户信息的url https://oapi.dingtalk.com/user/get?access_token=5a96c1b5b730351aa818077f637afe45&userid=0703165905-762610658
	$getUserData = ['access_token' => $tokenResponse->body->access_token, 'userid' => $ddUserId];
	$getUserUrl = $ddConfig['OAPI_HOST'] . '/user/get?' . http_build_query($getUserData);
	
	// $response->body->access_token;
	$userResponse = \Httpful\Request::get($getUserUrl)->send();
	if ($userResponse->hasErrors() || $userResponse->body->errcode != 0){
		sq_message(10002, '请求错误，错误码:10002'); // 获取用户信息错误
	}
	
	$result = json_encode($userResponse->body, JSON_FORCE_OBJECT); // 将用户信息(对象)转换为json
	$ddUser = json_decode($result, true); // 获得用户了
	
	// die; // 阻止用户保存
	
	$postData = [
// 			测试数据
// 			'email' => '010@163.com',
// 			'username' => 'test10',
			'create_on' => true, // 当关闭用户注册的时候，还能在钉钉里面进行注册
			'email' => $ddUser['email'],
			'username' => $ddUser['name'],
			'password' => md5($ddConfig['DEFAULT_PWD']), // 默认密码
			'ajax' => 1 // 将请求的类型告诉user-create操作
	];
	
	$url = $ddConfig['APP_HOST'] . '/?user-create.htm'; // 注册用户的地址
	$result = http_request($url, $postData); // 发送请求
	$result = json_decode($result, true); // 将json转换成数组
	
	if ($result && $result['code'] === 0) { // 如果解释上面返回的东西得出数组的话，并且数据里面的code为0，执行下面的操作
		$insertSuccess = dd_user_create($result['uid'], $ddUserId, $ddUser['name']); // 钉钉用户插入
		
		if ($insertSuccess === false) sq_message(10005, '操作失败，错误码：10005'); // 用户增加失败
		
		$_SESSION['uid'] = $result['uid']; // 保存用户登陆状态
		user_token_set($result['uid']); // 保存用户信息到cookie里面
		// $bbsToken = user_token_gen($result['uid']); // cookie里面的一个uid保存的加密信息
		
		/** 下载缩略图 */
		$imgUrl = $ddUser['avatar']; // 远程图片url
		$avatarPath = APP_PATH . 'upload/avatar/000/'; // 保存图片路径，用户头像的路径
		$imgName = $result['uid'] . '.png'; // 保存名
		
		$flag = get_compress_image($imgUrl, $avatarPath, $imgName); // 获得缩略图
		if ($flag['error']) {
			sq_message(0, '获取用户头像失败，请之后再上传头像！'); // 获取头像失败但不影响程序
		} else {
			user_update($result['uid'], array('avatar'=>$time)); // 更新用户表里面的头像数据
		}
		
		// 	sq_message(0, jump('登陆成功', http_referer(), 2));
		// sq_message(0, lang('user_login_successfully'), [], 3, './?my.htm'); // 这里等待够长了，直接跳转吧
		http_location('./?my.htm');
	} else { // 如果非json数据，提示错误
		sq_message(10003, $result['message']); // 非json返回的错误
	}
	
	
}
