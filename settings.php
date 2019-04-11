<?
session_start();
require_once('php/dbconfig.php');
require_once('header.php');
?>

<div class="left-bar static">

<?
if($_SESSION['user']){
	$conn = new mysqli(SERVER, USER, PWORD, DB);
	if($conn->connect_error){
		exit('Ошибка подключения к базе: ' . $conn->connect_error);
	}

	//$sql = 'SHOW TABLES';
	$sql = 'SELECT * FROM `FindReplace`';

	if($result = $conn->query($sql)){		
		foreach($result->fetch_all() as $item){
			echo $item[2] . '<br>';
		}
	}
	else{
		echo $conn->error;
	}
}
?>

</div>


<?
require_once('footer.php');
?>