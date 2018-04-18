<?php
exit;

if (isset($haya_post_info_config['at_user_to_notice']) 
	&& $haya_post_info_config['at_user_to_notice'] != 1
) {
	unset($notice_menu[156]);
}

?>