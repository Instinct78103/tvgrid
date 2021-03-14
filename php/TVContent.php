<?
require_once('functions.php');
require_once('class_channel.php');

//При нажатии на название канала в списке div.list, 
//формируется json-переменная "fileName", 
//затем появляется объект $obj = new Channel($arr['fileName']).

$jsonStr = file_get_contents('php://input');

$arr = json_decode($jsonStr, true);
$obj = new Channel($arr['fileName']);

session_start();
echo json_encode(
    [
        'fileName' => $obj->fileName,
        'startTime' => $obj->startTime,
        'endTime' => $obj->endTime,
        'afterDot' => $obj->afterDot,
        'raw' => $obj->getLines(),
        'result' => result($obj->raw())
    ],JSON_UNESCAPED_UNICODE);