<?
require('header.php');
unset($_SESSION['user']);
header('Location: http://' . $_SERVER['SERVER_NAME'] . '/index.php');
?>
