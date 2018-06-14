/** 用户是否登陆，如果是post请求，用户没有登陆的话，返回一个json **/
$action = param(1);
if($action == 'create' && empty($user)) {
    message(1, '请先登录');
}