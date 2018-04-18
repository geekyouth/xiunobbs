<?php exit;
if ($_forum['well_fup']) {
    $update = array('well_son-' => 1);
    // hook well_content_setting_column_delete_arr.php
    forum_update($_forum['well_fup'], $update);
}
?>