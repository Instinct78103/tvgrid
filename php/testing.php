<?
require_once('DBconfig.php');

function pre($arr){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

function firstLetterUpperCase($str){
	//Первая буква строки начнется с ЗАГЛАВНОЙ
	return mb_convert_case( mb_substr(trim($str), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8" ) . mb_substr(trim($str), 1, mb_strlen(trim($str), 'UTF8'),  'UTF8');
}

function cleaner(){
		
		//Оставляемые фразы
		
		$conn = new mysqli(SERVER, USER, PWORD, DB);
		if($conn->connect_error){
			exit('Ошибка подключения к базе: ' . $conn->connect_error);
		}
		
		$sql = 'SELECT `item` FROM `DeleteAll`';
		$result = $conn->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$findAndDelete[] = $row['item'];
			}
		}
		
		return $findAndDelete;
		
		/* $RealNames = 'SELECT `item` FROM `RealNames`';
		$result = $conn->query($RealNames);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$realNames[] = $row['item'];
			}
		}
		
		return $realNames; */
}

pre(cleaner());