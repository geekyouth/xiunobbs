<?php

// hook model_well_cms_form_start.php

// well_form_multi_checkbox('flag', array('1'=>'最新','2'=>'头条'), array('1','2'))
// name  选项内容  被选中选项(选项内容的键名)
function well_form_multi_checkbox($name, $arr, $checked = array())
{
    $s = '';
    $i = 0;
    foreach ($arr as $k => $v) {
        $i++;
        $ischecked = in_array($k, $checked);
        $s .= well_form_checkbox($name . '[' . $i . ']', $ischecked, $v) . ' ';
    }
    return $s;
}

function well_form_checkbox($name, $checked = 0, $txt = '')
{
    $add = $checked ? ' checked="checked"' : '';
    $s = "<label class=\"custom-input custom-checkbox mr-1\"><input type=\"checkbox\" name=\"$name\" value=\"1\" $add /> $txt</label>";
    return $s;
}

// hook model_well_cms_form_end.php

?>
