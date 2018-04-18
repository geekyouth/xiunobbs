// 公用的图片模板，采用函数，效率比 include 高。
function sq_post_img_list_html($imglist, $include_delete = FALSE) {
	if(empty($imglist)) return '';
	
	$s = '<fieldset class=imageset>'."\r\n";
	$s .= '<legend>上传的摄影图片：</legend>'."\r\n";
	$s .= '<ul class="attachlist">'."\r\n";
	foreach ($imglist as &$attach) {
		$s .= '<li aid="'.$attach['aid'].'">'."\r\n";
		/**
		$s .= '		<a href="'.url("attach-download-$attach[aid]").'" target="_blank">'."\r\n";
		$s .= '			<i class="icon filetype '.$attach['filetype'].'"></i>'."\r\n";
		$s .= '			'.$attach['orgfilename']."\r\n";
		$s .= '		</a>'."\r\n";
		**/
		
		$s .= '		<img alt="" src="' . $attach['url'] . '" style="width: 30%;">' . "\r\n";
		$s .= '		<div style="font-weight: bold;">照片名称：<a class="sq_exif_font" href="' . $attach['url'] . '" target="_blank">' . $attach['orgfilename'] . '</a></div>' . "\r\n";
		
		if(function_exists('exif_read_data')) {
			$img_exif = exif_read_data($attach['url'], 'IFD0');
			if(!empty($img_exif)) {
				// $exif_info = [
				// 	'相机品牌' => $img_exif['Make'],
				// 	'相机型号' => $img_exif['Model'],
				// 	'光圈'    => $img_exif['COMPUTED']['ApertureFNumber'],
				// 	'曝光时间' => $img_exif['ExposureTime'],
				// 	'焦距'    => $img_exif['FocalLength'],
				// 	'拍摄时间' => $img_exif['DateTimeOriginal']
				// ];
				// $attach['img_exif'] = $exif_info;
				// $attach['img_exif_all'] = $img_exif;
				$s .= '		<div class="sq_exif_info">' . "\r\n";
				$s .= '			相机品牌：<span class="sq_exif_font">' . $img_exif['Make'] . '</span>&nbsp;&nbsp;';
				$s .= '相机型号：<span class="sq_exif_font">>' . $img_exif['Model'] . '</span>&nbsp;&nbsp;';
				$s .= '光圈：<span>' . $img_exif['Model'] . '</span>&nbsp;&nbsp;';
				$s .= '曝光时间：<span>' . $img_exif['ExposureTime'] . '</span>&nbsp;&nbsp;';
				$s .= '焦距：<span>' . $img_exif['FocalLength'] . '</span></br>';
				$s .= '拍摄时间：<span>' . $img_exif['DateTimeOriginal'] . '</span>' . "\r\n";
				$s .= '		</div>' . "\r\n";
			}
		} else {
			$s .= '		<div class="sq_no_exif">' . "\r\n";
			$s .= '		你并没有安装php的exif模块，想查看元数据，请先开启！';
			$s .= '		</div>' . "\r\n";
		}

		$include_delete AND $s .= '		<a href="javascript:void(0)" class="delete ml-3"><i class="icon-remove"></i> '.lang('delete').'</a>'."\r\n";
		$s .= '</li>'."\r\n";
	};
	$s .= '</ul>'."\r\n";
	$s .= '</fieldset>'."\r\n";
	
	return $s;
}