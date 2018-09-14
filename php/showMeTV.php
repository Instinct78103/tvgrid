<?
include('functions.php');
include('class_channel.php');

$jsonStr = file_get_contents('php://input');
$arr = json_decode($jsonStr, true);
$obj = new Channel($arr['fileName']);

result( $obj->raw() );