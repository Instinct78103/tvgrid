<?
include('functions.php');
include('class_channel.php');

//При нажатии на название канала в списке div.list, 
//формируется json-переменная "fileName", 
//затем появляется объект $obj = new Channel($arr['fileName']).

$jsonStr = file_get_contents('php://input');
$arr = json_decode($jsonStr, true);
$obj = new Channel($arr['fileName']);

result( $obj->raw() );