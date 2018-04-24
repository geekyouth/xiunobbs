$ddPluginConf = file_get_contents(APP_PATH . '/plugin/sq_dd_autoLogin/conf.json');
$ddPluginConf = json_decode($ddPluginConf, true);
if ($ddPluginConf['enable'] == 1) { // 如果钉钉自动登陆开启，那么用户名不进行验证
	return true;
}