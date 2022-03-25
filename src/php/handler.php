<?php
require_once('functions.php');
$jsonStr = file_get_contents('php://input');

$arr = json_decode($jsonStr, true);

$finalOutput = [];

if (isset($arr['fileName'])) {
    $obj = new Channel($arr['fileName']);
    $finalOutput =
        [
            'startTime' => $obj->startTime,
            'endTime' => $obj->endTime,
            'afterDot' => $obj->afterDot,
            'lowerCase' => $obj->lowerCase,
            'raw' => join("\n", $obj->getLinesUTF8()),
            'result' => result($obj->getLinesByFileName()),
        ];
} else {
    $_POST = json_decode($jsonStr, true);
    $finalOutput =
        [
            'startTime' => $_POST['startTime'],
            'endTime' => $_POST['endTime'],
            'raw' => $_POST['txt_in'],
            'result' => result(getLinesByJSEvent($_POST['startTime'], $_POST['endTime'])),
        ];
}

echo json_encode($finalOutput, JSON_UNESCAPED_UNICODE);