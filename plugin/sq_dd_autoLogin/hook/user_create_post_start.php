$ddPluginConf = file_get_contents(APP_PATH . '/plugin/sq_dd_autoLogin/conf.json');
$ddPluginConf = json_decode($ddPluginConf, true);
if ($ddPluginConf['enable'] == 1 && $ajax == 1) {
	global $ajax;
	$ajax = 1;
}