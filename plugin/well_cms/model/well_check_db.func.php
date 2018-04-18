<?php

// 检查表
function well_db_find_table($table)
{
    if ($table) {
        $arr = db_sql_find_one("SHOW TABLES LIKE '{$table}'");
        if (!empty($arr)) {
            foreach ($arr as $v) {
                if ($v == $table) return TRUE;
            }
        }
    }
    return FALSE;
}

// 检查字段 表 字段
function well_db_find_field($table, $field)
{
    if ($table && $field) {
        $r = db_sql_find_one("DESCRIBE " . $table . " `{$field}`");
        if (!empty($r) && $r['Field'] == $field) return TRUE;
    }
    return FALSE;
}

// 检查索引 表 索引
function well_db_find_index($table, $index)
{
    if ($table && $index) {
        $arr = db_sql_find("SHOW INDEX FROM " . $table);
        if (!empty($arr)) {
            foreach ($arr as $v) {
                if ($v['Key_name'] == $index) return TRUE;
            }
        }
    }
    return FALSE;
}

?>