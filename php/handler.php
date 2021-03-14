<?
require_once('functions.php');

$jsonStr = file_get_contents('php://input');
$_POST = json_decode($jsonStr, true);
echo json_encode(
    ['result' => result( getArray() )], JSON_UNESCAPED_UNICODE);