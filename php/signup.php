<?php
require_once('dbconfig.php');
require_once('../header.php');

if(isset($_POST['signup']))
{	
	$errors = [];
	if(trim($_POST['email']) == '')
	{
		$errors[] = 'Введите email!';
	}
	
	if($_POST['pword'] == '')
	{
		$errors[] = 'Введите пароль!';
	}
	
	if($_POST['pword'] != $_POST['rpword'])
	{
		$errors[] = 'Пароли не совпадают!';
	}
	
	if(empty($errors))
	{
		//Все хорошо!
		$conn = mysqli_connect(SERVER, USER, PWORD, DB);
		if(mysqli_connect_errno()){
			exit('Ошибка подключения к базе: ' . mysqli_connect_error());
		}
		
		$email = $_POST['email'];
		$pword = $_POST['pword'];
		
		$sql = "INSERT INTO `users`(`userID`, `email`, `password`) values(null, '$email', '$pword')";
		mysqli_query($conn, $sql) or die(mysqli_error($conn));
		
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
require_once('../footer.php');
?>