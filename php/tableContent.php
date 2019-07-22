<?
session_start();
require_once(__DIR__ . '/define.php');

if($_SESSION['user']){
	$jsonStr = file_get_contents('php://input');
	$tableChosen = json_decode($jsonStr, true)['tableName'];
	
	$sql_arr = [
		//Запросы для каждой таблицы
		'DeleteAll' 		=> "SELECT `item`
								FROM {$tableChosen}  
								WHERE `UserID` = {$_SESSION['user'][0]} 
								ORDER BY `item` ASC",
		
		'DeleteAllExcept'	=>	"SELECT `item`
								FROM {$tableChosen}
								WHERE `UserID` = {$_SESSION['user'][0]}
								ORDER BY `item` ASC",
								
		'FindReplace' 		=> "SELECT `find_what`, `replace_with`
								FROM {$tableChosen}  
								WHERE `UserID` = {$_SESSION['user'][0]}
								ORDER BY `find_what` ASC",
		
		'RealNames' 		=> "SELECT `item`
								FROM {$tableChosen}  
								WHERE `UserID` = {$_SESSION['user'][0]}
								ORDER BY `item` ASC",
		
		'Users'				=> "SELECT `email`, `password`
								FROM {$tableChosen}  
								WHERE `UserID` = {$_SESSION['user'][0]}"
	];
	
	$conn = new mysqli(SERVER, USER, PWORD, DB);
	if($conn->connect_error){
		exit('Ошибка подключения к базе: ' . $conn->connect_error);
	}
	
	$sql = $sql_arr[$tableChosen];
	$result = $conn->query($sql) or die($conn->error);
	//$data = $result->fetch_all(MYSQLI_ASSOC);
	$data = $result->fetch_all();
	$conn->close();
	
	/* echo '<pre>';
	print_r($data);
	echo '</pre>'; */
	
	if($tableChosen != 'Users'){
		echo "<table id=\"{$tableChosen}\">";
			echo '<tr>';
			for($i = 0; $i < count($data[0]); $i++){
				//echo '<td id="new_row" contenteditable="true" oninput="myFunc(this.innerText)"></td>';
				echo '<td><input id="inputTr" type="text" placeholder="Добавить..." onchange="myFunc(this.id)"></td>';
			}
			echo '</tr>';
			foreach($data as $item){
				echo '<tr>';
				foreach($item as $elem){
					echo '<td contenteditable="true"><pre>' . htmlspecialchars($elem) . '</pre></td>';
				}
				echo '</tr>';
			}
		echo '</table>';
	}
	else{
		echo 'Данные пользователя';
	}
}
?>
