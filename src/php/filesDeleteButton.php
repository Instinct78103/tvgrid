<?php
require_once('define.php');
$buttonClicked = file_get_contents('php://input');
$dir = '';
if ($buttonClicked === 'delete_txt') {
    $dir = TXT_DIR;
} elseif ($buttonClicked === 'delete_docx') {
    $dir = DOC_DIR;
}

$f_arr = array_values(array_diff(scandir($dir), ['.', '..']));
if (count($f_arr)) {
    foreach ($f_arr as $item) {
        unlink("$dir/$item");
    }
}