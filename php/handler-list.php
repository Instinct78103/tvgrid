<?php
require_once('define.php');

$arr = [];
$each_dir_files = [];
$dirs = [TXT_DIR, DOC_DIR];

foreach ($dirs as $dir) {
    $each_dir_files[$dir] = array_values(array_diff(scandir($dir), ['.', '..']));
}

foreach ($each_dir_files as $dir => $files){
    foreach ($files as $file){
        if ($file && round(date(time() - filemtime($dir.$file)) / (24 * 60 * 60), 2) > 5.5) {
            unlink($dir . $file);
        } else {
            $arr[str_replace('/','', $dir)][] = $file;
        }
    }
}

echo json_encode($arr);