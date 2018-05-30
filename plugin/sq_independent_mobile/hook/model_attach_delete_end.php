<?php exit;
$tid = $attach['tid'];
if($attach['is_image']){
    db_update('thread', ['tid' => $tid], ['image-' => 1]);
}
