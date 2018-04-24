// 读取钉钉自动登陆插件的配置，如果enable，则直接跳转，不告诉用户已经注册成功
if ($ddPluginConf['enable'] == 1) {
	echo json_encode(['code' => 0, 'msg' => '注册成功', 'uid' => $uid]); die;
	// message(0, '注册成功');
}