<?php
!defined('DEBUG') AND exit('Access Denied.');

//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
function curl_request($url, $post='', $cookie='', $returnCookie=0){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
	if($post) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
	}
	if($cookie) {
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	}
	curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	$data = curl_exec($curl);
	if (curl_errno($curl)) {
		return curl_error($curl);
	}
	curl_close($curl);
	if($returnCookie){
		list($header, $body) = explode("\r\n\r\n", $data, 2);
		preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
		$info['cookie']  = substr($matches[1][0], 1);
		$info['content'] = $body;
		return $info;
	}else{
		return $data;
	}
}

/**
 * 通用CURL请求
 * @param $url  需要请求的url
 * @param null $data
 * return mixed 返回值 json格式的数据
 */
function http_request($url, $data = null)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	if (!empty($data)) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$info = curl_exec($curl);
	curl_close($curl);
	return $info;
}

function grabImage($url, $filename = "") {
	 if ($url == ""):return false;
	 endif;
	 //如果$url地址为空，直接退出
	 if ($filename == "") {
	 //如果没有指定新的文件名
	 $ext = strrchr($url, ".");
	 //得到$url的图片格式
	 if ($ext != ".gif" && $ext != ".jpg"):return false;
	 endif;
	 //如果图片格式不为.gif或者.jpg，直接退出
	 $filename = date("dMYHis") . $ext;
	 //用天月面时分秒来命名新的文件名
	 } 
	 ob_start();//打开输出
	 readfile($url);//输出图片文件
	 $img = ob_get_contents();//得到浏览器输出
	 ob_end_clean();//清除输出并关闭
	 $size = strlen($img);//得到图片大小
	 $fp2 = @fopen($filename, "a");
	 fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
	 fclose($fp2);
	 return $filename;//返回新的文件名
}

/**
 *功能：php完美实现下载远程图片保存到本地
 *参数：文件url,保存文件目录,保存文件名称，使用的下载方式
 *当保存文件名称为空时则使用远程文件原来的名称
 *@param string $url 文件url
 *@param string $save_dir 保存文件目录
 *@param string $filename 保存文件名称
 *@param int $type 使用的下载方式，默认0为ob，1为curl
 */
function get_image($url,$save_dir='',$filename='',$type=0){
	if(trim($url)==''){
		return array('file_name'=>'','save_path'=>'','error'=>1);
	}
	if(trim($save_dir)==''){
		$save_dir='./';
	}
	if(trim($filename)==''){//保存文件名
		/* 原来的判断
		 $ext=strrchr($url,'.');
		 if($ext!='.gif'&&$ext!='.jpg'&&$ext!='.png'){
		 return array('file_name'=>'','save_path'=>'','error'=>3);
		 }
		 $filename=time().$ext;
		 */
		// 保存原来的文件名
		if(!preg_match('/\/([^\/]+\.[a-z]{3,4})$/i', $url, $matches)){
			return array('file_name'=>'','save_path'=>'','error'=>3);
		}
		$filename = strToLower($matches[1]);
	}
	if(0!==strrpos($save_dir,'/')){
		$save_dir.='/';
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return array('file_name'=>'','save_path'=>'','error'=>5);
	}
	
	//获取远程文件所采用的方法
	if($type){
		$ch=curl_init();
		$timeout=5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$img=curl_exec($ch);
		curl_close($ch);
	}else{
		ob_start();
		readfile($url);
		$img=ob_get_contents();
		ob_end_clean();
	}
	//$size=strlen($img);
	//文件大小
	$fp2=@fopen($save_dir.$filename,'a');
	fwrite($fp2,$img);
	fclose($fp2);
	unset($img,$url);
	return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}

function saveImage($path) {
	if(!preg_match('/\/([^\/]+\.[a-z]{3,4})$/i',$path,$matches)) die('Use image please');
	$image_name = strToLower($matches[1]);
	$ch = curl_init ($path);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	$img = curl_exec ($ch);
	curl_close ($ch);
	$fp = fopen($image_name,'w');
	fwrite($fp, $img);
	fclose($fp);
}

/**
 * 下载压缩后的图片（没用）
 * @param unknown $url
 * @param unknown $percent
 */
function get_compress_image($url, $save_dir='', $filename='') {
	if(trim($url)==''){
		return array('file_name'=>'','save_path'=>'','error'=>1);
	}
	if(trim($save_dir)==''){
		$save_dir='./';
	}
	if(trim($filename)==''){//保存文件名
		/* 原来的判断
		 $ext=strrchr($url,'.');
		 if($ext!='.gif'&&$ext!='.jpg'&&$ext!='.png'){
		 return array('file_name'=>'','save_path'=>'','error'=>3);
		 }
		 $filename=time().$ext;
		 */
		// 保存原来的文件名
		if(!preg_match('/\/([^\/]+\.[a-z]{3,4})$/i', $url, $matches)){
			return array('file_name'=>'','save_path'=>'','error'=>3);
		}
		$filename = strToLower($matches[1]);
	}
	if(0!==strrpos($save_dir,'/')){
		$save_dir.='/';
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return array('file_name'=>'','save_path'=>'','error'=>5);
	}
	
	list($width, $height, $type, $attr) = getimagesize($url);
	$imageInfo = array(
			'width' => $width,
			'height' => $height,
			'type' => image_type_to_extension($type,false),
			'attr' => $attr
	);
	$fun = 'imagecreatefrom' . $imageInfo['type'];
	$simg = $fun($url);
	// 将图片宽高等比60%保存
	$new_width = $imageInfo['width'] * 0.6;
	$new_height = $imageInfo['height'] * 0.6;
	$image_thump = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($image_thump, $simg, 0, 0, 0, 0, $new_width, $new_height, $imageInfo['width'], $imageInfo['height']);
	imagedestroy($simg);
	$funcs = 'image'.$imageInfo['type'];
	$funcs($image_thump, $save_dir . $filename);
	
	return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}

/*
 message(0, '登录成功');
 message(1, '密码错误');
 message(-1, '数据库连接失败');
 
 code:
 < 0 全局错误，比如：系统错误：数据库丢失连接/文件不可读写
 = 0 正确
 > 0 一般业务逻辑错误，可以定位到具体控件，比如：用户名为空/密码为空
 */
function sq_message($code, $message, $extra = array(), $jumpTime = 3, $jumpHref = './') {
	global $ajax, $header, $conf;
	
	$arr = $extra;
	$arr['code'] = $code.'';
	$arr['message'] = $message;
	$header['title'] = $conf['sitename'];
	
	// hook model_message_start.php
	
	// 防止 message 本身出现错误死循环
	static $called = FALSE;
	$called ? exit(xn_json_encode($arr)) : $called = TRUE;
	if($ajax) {
		echo xn_json_encode($arr);
	} else {
		if(IN_CMD) {
			if(is_array($message) || is_object($message)) {
				print_r($message);
			} else {
				echo $message;
			}
			exit;
		} else {
			if(defined('MESSAGE_HTM_PATH')) {
				include _include(MESSAGE_HTM_PATH);
			} else {
				// 判断是否有开启手机独立
				$indepConfig = file_get_contents(APP_PATH . "plugin/sq_dd_autoLogin/conf.json");
				$indepConfig = json_decode($indepConfig, true);
				if ($indepConfig['enable'] && sq_is_mobile()) {
					$show_search = 2; // 不显示搜索栏
					include APP_PATH . "plugin/sq_dd_autoLogin/view/htm/indep_message.htm";
				} else {
					include APP_PATH . "plugin/sq_dd_autoLogin/view/htm/message.htm";
				}
			}
		}
	}
	// hook model_message_end.php
	exit;
}

/** 根据钉钉用户id读取用户信息 */
function dd_user_read($ddUserId) {
	$db = $_SERVER['db'];
	$tablepre = $db->tablepre;
	$sql = "SELECT * FROM {$tablepre}sq_dduser a JOIN {$tablepre}user b ON a.u_id = b.uid WHERE a.dd_id = '$ddUserId'";
	$user = $db->sql_find_one($sql);
	return $user;
}

/** 插入钉钉用户信息 */
function dd_user_create($uid, $ddUserId, $ddUsername) {
	$arr = ['u_id' => $uid, 'dd_id' => $ddUserId, 'dd_name' => $ddUsername];
	$result = db_insert('sq_dduser', $arr);
	return $result;
}

/** 判断是否手机浏览 */
function sq_is_mobile() {
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
		return true;
	}
	// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset($_SERVER['HTTP_VIA'])) {
		// 找不到为flase,否则为true
		return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	}
	// 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
			return true;
		}
	}
	// 协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT'])) {
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
			return true;
		}
	}
	return false;
}