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
	
	//Согласно первому запросу мы получаем все столбцы соответствующей таблицы
	//Первый запрос нужен, чтобы сформировать заголовки столбцов
	$sql0 = "SHOW COLUMNS FROM `{$arr['tableName']}`";
	
	$sql = "SELECT * FROM {$arr['tableName']} 
			WHERE `UserID` = {$_SESSION['user'][0]}";

	$result0 = $conn->query($sql0) or die($conn->error);
	$result = $conn->query($sql) or die($conn->error);
	
	//$data0 и $data - массивы данных. Позже удаляем UserID в этих массивах
	$data0 = $result0->fetch_all(MYSQLI_ASSOC);
	$data = $result->fetch_all(MYSQLI_ASSOC);
	
	
	unset($data0[0]); //Удаляем столбец UserID среди заголовков столбцов
	foreach($data as $key=>$item){
		unset($data[$key]['userID']); //Удаляем столбец UserID среди значений согласно второму запросу $sql
	}
	
	$conn->close();
	
	echo '<table>';
		echo '<tr>';
		foreach($data0 as $item){
			echo '<th>' . $item['Field'] . '</th>';
		}
		echo '</tr>';
		
		foreach($data as $item){
			echo '<tr>';
			foreach($item as $key=>$item2){
				echo '<td>' . $item2 . '</td>';
			}
			echo '</tr>';	
		}
	echo '</table>';
}