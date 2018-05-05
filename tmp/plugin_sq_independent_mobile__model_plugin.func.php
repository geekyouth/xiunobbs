<?php 
// 根据帖主题查找图片,由于部分图片太大并且没有缩略图，所以这里先限制可显示的大小
function get_images_by_tid($tid, $isThumb = false) {
	$data = db_find('attach', ['tid' => $tid, 'isimage' => 1], ['aid' => 1], 1, 3, '', ['aid', 'filename']);
	if($isThumb) {
		$uploadPath = './upload/attach/';
		$path = './upload/attach/thumb/'; // 缩略图目录
		
		foreach($data as &$item) {
			$originFilename = $item['filename'];
			$item['filename'] = str_replace(".", ".thumb.", $item['filename']);
			$filename = explode('/', $item['filename']);
			$day = $filename[0];
			$filename = $filename[1]; // 取后面的文件名
			if(!file_exists($path . $item['filename'])) { // 如果没有该缩略图的话，生成一个
				get_compress_image($uploadPath . $originFilename, $path . $day . '/', $filename);
			}
		}
	}
	return $data;
}

/** 根据id获得帖子的内容简介 */
function get_desc_by_tid($tid) {
	$data = db_find_one('post', ['tid' => $tid, 'isfirst' => 1], [], ['message_fmt']);
	$data = preg_replace('/<\/?.+?>/', '', $data);
	$data = cut_str($data['message_fmt'], 50); // 获得前50个字符
	if($data) $data .= '...'; // 加上省略号
	return $data;
}

/**
 * 下载压缩后的图片
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

 
/** 截取中文字符串  */
/* 
Utf-8、gb2312都支持的汉字截取函数 
cut_str(字符串, 截取长度, 开始长度, 编码); 
编码默认为 utf-8 
开始长度默认为 0 
*/function cut_str($string, $sublen, $start = 0, $code = 'UTF-8') 
{ 
	if($code == 'UTF-8') { 
		$pa ="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/"; 
		preg_match_all($pa, $string, $t_string);
		if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)); 
		return join('', array_slice($t_string[0], $start, $sublen)); 
	} else { 
		$start = $start*2; 
		$sublen = $sublen*2; 
		$strlen = strlen($string); 
		$tmpstr = '';
		for($i=0; $i<$strlen; $i++) { 
			if($i>=$start && $i<($start+$sublen)) { 
				if(ord(substr($string, $i, 1))>129) { 
					$tmpstr.= substr($string, $i, 2); 
				} 
				else { 
					$tmpstr.= substr($string, $i, 1); 
				} 
			} 
			if(ord(substr($string, $i, 1))>129) $i++; 
		} 
		if(strlen($tmpstr) < $strlen) $tmpstr.= ""; 
		return $tmpstr; 
	} 
} 
	