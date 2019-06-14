<?
session_start();
require_once('php/define.php');
require_once('header.php');
?>

<div class="left-bar">

<?
if($_SESSION['user']){
	$conn = new mysqli(SERVER, USER, PWORD, DB);
	if($conn->connect_error){
		exit('Ошибка подключения к базе: ' . $conn->connect_error);
	}

	$sql = 'SHOW TABLES';
	$sql2 = 'SELECT table_comment 
			FROM information_schema.tables
			WHERE table_schema = "TV"';
			
	$result = $conn->query($sql) or die($conn->error);
	$result2 = $conn->query($sql2) or die($conn->error);

		
		$tables = $result->fetch_all();
		$tables_comm = $result2->fetch_all();
		
		echo '<ul class="tables">';
		foreach($tables as $key=>$item){
			echo '<li id="' . $tables[$key][0] . '">' . $tables_comm[$key][0] . '</li>';
		}
		echo '</ul';
}
else{
	//Если пользователь не авторизован, 
	//но оказался на этой странице, просто введя адрес
}
?>

</div>


<?
require_once('footer.php');
?>