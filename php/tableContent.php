<?
session_start();
require_once(__DIR__ . '/define.php');

$jsonStr = file_get_contents('php://input');
$arr = json_decode($jsonStr, true);
//print_r($arr['tableName']);

if($_SESSION['user']){
	$conn = new mysqli(SERVER, USER, PWORD, DB);
	if($conn->connect_error){
		exit('Ошибка подключения к базе: ' . $conn->connect_error);
	}
	
	$sql = "SELECT `item` FROM {$arr['tableName']} 
			WHERE `UserID` = {$_SESSION['user'][0]} 
			ORDER BY `item` ASC";

	$result = $conn->query($sql) or die($conn->error);
	
	print_r($result->fetch_all(MYSQLI_ASSOC));
}