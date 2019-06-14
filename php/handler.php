<?
require_once('functions.php');

$jsonStr = file_get_contents('php://input');
$_POST = json_decode($jsonStr, true);

result( getArray() );