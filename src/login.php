<?php
session_start();
require_once('php/define.php');

$email = '';
$errors = [];

if (isset($_POST['login'])) {

	$conn = new mysqli(SERVER, USER, PWORD, DB);
	if ($conn->connect_error) {
		exit('Ошибка подключения к базе: ' . $conn->connect_error);
	}

	$email = $conn->real_escape_string($_POST['email']);
	$pword = $conn->real_escape_string($_POST['pword']);

	$query = "SELECT `id`, `email`, `password` FROM `users` WHERE `email` = ? LIMIT 0,1";

	$stmt = $conn->prepare($query);
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$num = $stmt->get_result()->fetch_assoc();

	if (count($num) === 3) {
		$id = $num['id'];
		$email = $num['email'];
		$pwd2 = $num['password'];

		if (password_verify($pword, $pwd2)) {
			$_SESSION['user'] = $num;
			header('Location: /');
			exit;
		} else {
			$errors[] = 'Неверный email или пароль!';
		}
	} else {
		$errors[] = 'Пользователь не найден!';
	}

	$conn->close();

	if (!empty($errors)) {
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
