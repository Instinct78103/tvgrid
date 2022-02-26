<?php
session_start();
require_once('php/define.php');

if(isset($_POST['login']))
{
	$errors = [];
	
	$conn = new mysqli(SERVER, USER, PWORD, DB);
	if($conn->connect_error){
		exit('Ошибка подключения к базе: ' . $conn->connect_error);
	}
	
	$email = $conn->real_escape_string($_POST['email']);
	$pword = $conn->real_escape_string($_POST['pword']);
	
	$sql = "SELECT * from `users` 
			WHERE `email` = '$email'";
			
	if($result = $conn->query($sql)){
		if($result->num_rows){
			
			$sql2 = "SELECT * from `users`
			WHERE `email` = '$email'
			AND `password` = '$pword'";
			
			if($result2 = $conn->query($sql2)){
				if($result2->num_rows){
					// Массив со значениями из разных столбцов (userID, email, password)
					$_SESSION['user'] = $result2->fetch_row();
					header('Location: http://' . $_SERVER['SERVER_NAME']);
					exit;
				}
				else{
					$errors[] = 'Неверно введен пароль!';
				}
			}
		}
		else{
			$errors[] = 'Пользователь не найден!';
		}
	}
	
	$conn->close();
	
	if(!empty($errors)){
		echo '<div 
		style="position: fixed;  
		right: 20px; 
		bottom: 0; 
		padding: 30px;
		color: red; 
		background-color: #FFF;
		border: 1px solid #bbbbbb;">' . array_shift($errors) . '</div>';
	}
	
}
require_once('header.php');
?>

<div class="left-bar">
	<form method="POST">
		<p><input type="text" name="email" placeholder="Email" value="<?php echo $email; ?>"></p>
		<p><input type="password" name="pword" placeholder="Пароль"></p>
		<p><input type="submit" name="login" value="Войти"></p>
	</form>
</div>

<?php
require_once('footer.php');
?>
	