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

