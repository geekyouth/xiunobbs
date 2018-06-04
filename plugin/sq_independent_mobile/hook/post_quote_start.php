    $s = post_brief($s, 100);
	$userhref = url("user-$uid");
	$user = user_read_cache($uid);
	$r = '<blockquote class="blockquote">
        <span class="text-muted">回复：&nbsp;</span>
		<a href="'.$userhref.'" class="text-small text-muted user">
			'.$user['username'].'
		</a><br>
		'.$s.'</blockquote>';
	
	return $r;