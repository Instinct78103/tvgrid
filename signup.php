<?php
session_start();
require_once('php/define.php');


if(isset($_POST['signup']))
{	
	$errors = [];
	if(trim($_POST['email']) == '')
	{
		$errors[] = 'Введите email!';
	}
	elseif(!preg_match('~[0-9a-z\._-]{2,20}@[a-z0-9\._-]{1,25}\.[a-z]{2,10}~ui', $_POST['email'])){
		$errors[] = 'Некорректный email!';
	}
	else{
		//Проверка, есть ли такой email в базе
		$conn = new mysqli(SERVER, USER, PWORD, DB);
		if($conn->connect_error){
			exit('Ошибка подключения к базе: ' . $conn->connect_error);
		}
		$email = $_POST['email'];
		$sql = "SELECT count(`email`) 
				FROM `users` 
				WHERE `email` = '$email'";
		
		if($result = $conn->query($sql)){
			if($result->fetch_row()[0] > 0){
				$errors[] = 'Такой email уже зарегистрирован!';
			}
		}
		$conn->close();
	}
	
	if($_POST['pword'] == '')
	{
		$errors[] = 'Введите пароль!';
	}
	elseif( !preg_match('~^[а-яa-z0-9]{7,15}$~ui', $_POST['pword'])){
		$errors[] = 'Только буквы и цифры в пароле (от 7 символов)';
	}
	
	if($_POST['pword'] != $_POST['rpword'])
	{
		$errors[] = 'Пароли не совпадают!';
	}
	
	if(empty($errors))
	{
		//Все хорошо!
		$conn = new mysqli(SERVER, USER, PWORD, DB);
		if($conn->connect_error){
			exit('Ошибка подключения к базе: ' . $conn->connect_error);
		}
		
		$email = $_POST['email'];
		$pword = $_POST['pword'];
		
		$sql = "INSERT INTO `users`(`userID`, `email`, `password`) values (null, '$email', '$pword')";
		$result = $conn->query($sql) or die($conn->error);
		
		$conn->close();
		
		header('Location: http://' . $_SERVER['SERVER_NAME'] . '/login.php');		
	}
	else
	{
		echo '<div 
		style="position: absolute;  
		right: 20px; 
		top: 0; 
		padding: 30px;
		color: red; 
		background-color: #FFF;
		border: 1px solid #bbbbbb;
		border-top: none;">' . array_shift($errors) . '</div>';
	}

}
require_once('header.php');
?>

<div class="left-bar">
	<form method="POST">
		<p><input type="text" name="email" placeholder="Email" value="<? echo $_POST['email']; ?>"></p>
		<p><input type="password" name="pword" placeholder="Пароль"></p>
		<p><input type="password" name="rpword" placeholder="Повтор пароля"></p>
		<p><input type="submit" name="signup" value="Регистрация"></p>
	</form>
</div>

<?
require_once('footer.php');
?>