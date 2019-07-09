<?
session_start();
require_once(__DIR__ . '/define.php');

if($_SESSION['user']){
	
	$jsonStr = file_get_contents('php://input');
	$tableChosen = json_decode($jsonStr, true)['tableName'];
	
	$sql_arr = [
		//Запросы для каждой таблицы
		'DeleteAll' 		=> "SELECT `id`, `item`
								FROM {$tableChosen}  
								WHERE `UserID` = {$_SESSION['user'][0]} 
								ORDER BY `item` ASC",
		
		'DeleteAllExcept'	=>	"SELECT `id`, `item`
								FROM {$tableChosen}
								WHERE `UserID` = {$_SESSION['user'][0]}
								ORDER BY `item` ASC",
								
		'FindReplace' 		=> "SELECT `id`, `find_what`, `replace_with`
								FROM {$tableChosen}  
								WHERE `UserID` = {$_SESSION['user'][0]}
								ORDER BY `find_what` ASC",
		
		'RealNames' 		=> "SELECT `id`, `item`
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
	
/* 	echo '<pre>';
	print_r($data);
	echo '</pre>'; */
	
	$conn->close();
	
 	echo '<table>';
		
		/* echo '<tr>';
		while($col = $result->fetch_field()){
			echo '<th>' . $col->name . '</th>';
		}
		echo '</tr>'; */
		
		foreach($data as $item){
			echo "<tr id=\"{$item[0]}\">";
				echo '<td>' . $item[1] . '</td>'; echo ($item[2]) ? "<td>$item[2]</td>" : '';
			echo '</tr>';
		}
		
		/* foreach($data as $item){
			echo "<tr id=\"{$item['id']}\">";
				echo '<td>' . $item['item'] . '</td>';
			echo '</tr>';
		} */
	

/* 		echo '<tr>';
		while($col = $result->fetch_field()){
			echo '<th>' . $col->name . '</th>';
		}
		echo '</tr>';
		
		foreach($data as $item){
			echo '<tr>';
			foreach($item as $key=>$item2){
				echo '<td>' . $item2 . '</td>';
			}
			echo '</tr>';	
		} */
		
	echo '</table>';
}