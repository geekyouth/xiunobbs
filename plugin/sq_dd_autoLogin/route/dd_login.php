<?php

!defined('DEBUG') AND exit('Access Denied.');

require_once(APP_PATH . "plugin/sq_dd_autoLogin/vendor/nategood/httpful/bootstrap.php"); // 阿里的接口访问类

require_once(APP_PATH . 'plugin/sq_dd_autoLogin/vendor/image/Image.php');

include _include(APP_PATH . 'plugin/sq_dd_autoLogin/model/dd_login.func.php');
$ddConfig = include APP_PATH . 'plugin/sq_dd_autoLogin/config.php';

// 先获得用户的钉钉user_id(前端部分工作)

// 根据这个user_id获得用户的信息
$ddUserId = param('user_id');
$ddUserId = '1546114653-2075071073'; // 测试 rex

$postData = ['corpid' => $ddConfig['CORPID'], 'corpsecret' => $ddConfig['SECRET']];

/** 成功逻辑 */
// gettoken?corpid=dingde55314a8e20f3f6&corpsecret=L0k5CtQscvusMrbC4aLoVUv_W7VHC9AnYGH0roxD3w6dFlB8DNquukbYMcstqZ2R
$url = $ddConfig['OAPI_HOST'] . '/gettoken?' . http_build_query($postData);

$response = \Httpful\Request::get($url)->send();
if ($response->hasErrors())
{
	var_dump($response);
}
if ($response->body->errcode != 0)
{
	// 如果有错误的话
}
// https://oapi.dingtalk.com/user/get?access_token=5a96c1b5b730351aa818077f637afe45&userid=0703165905-762610658
$url = $ddConfig['OAPI_HOST'] . '/user/get?' . http_build_query(['access_token' => $response->body->access_token, 'userid' => $ddUserId]);

// $response->body->access_token;
$response = \Httpful\Request::get($url)->send();
if ($response->hasErrors()){
	var_dump($response);
}
if($response->body->errcode != 0) {
	var_dump($response);
}

$result = json_encode($response->body, JSON_FORCE_OBJECT);
$ddUser = json_decode($result, true); // 获得用户了

$imgPath = $ddUser['avatar'];//远程URL 地址
echo $imgPath;
// $tempPath = APP_PATH . 'upload/avatar/000/' . $result['uid'] . '.png';//保存图片路径
$tempPath = APP_PATH . 'upload/avatar/000/test.png';//保存图片路径
// $bigImg = grabImage($imgPath, $tempPath);
// var_dump(compressImg($bigImg,100,100,1));

// $file = get_image($imgPath, APP_PATH . 'upload/avatar/000', '', 1);
// $downResult = saveImage($imgPath);

// $img = file_get_contents($imgPath);
// file_put_contents($tempPath, $img);

// $image = new Image(APP_PATH . 'upload/avatar/000/');
// $image->thumb($file, 100, 100);

// echo '<pre>';
// var_dump($ddUser);
// echo '</pre>';
die;

$postData = [
		'email' => $ddUser['email'],
		'username' => $ddUser['name'],
// 		'email' => '010@163.com',
// 		'username' => 'test10',
		'password' => md5('123456'), // 默认密码
		'ajax' => 1
];
// var_dump($postData);
$url = 'http://loc.xiunobbs.com/?user-create.htm';
// echo $url;
$result = http_request($url, $postData);
var_dump($result);
$result = json_decode($result, true);
var_dump($result);

$_SESSION['uid'] = $result['uid'];
user_token_set($result['uid']);
// $bbsToken = user_token_gen($result['uid']);

if($result['code'] == 0) {
	user_token_set($result['uid']);
// 	message(0, jump('登陆成功', http_referer(), 2));
	http_location('http://loc.xiunobbs.com/');
}

