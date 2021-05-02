<?php
require_once('define.php');
$dir = FOLDER;

$f_arr = array_values(array_diff(scandir($dir), ['.', '..']));
$arr = [];
foreach ($f_arr as $item) {
    if ($item && round(date(time() - filemtime("$dir/$item")) / (24 * 60 * 60), 2) > 5.5) {
        unlink("$dir/$item");
    } else {
        $arr[] = $item;
    }
}

echo json_encode($arr);
?>