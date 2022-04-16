<?php

require_once 'DatabaseService.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$email = '';
$pwd = '';

$dbService = new DatabaseService();
$conn = $dbService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$pwd = $data->pwd;

$pwd = password_hash($pwd, PASSWORD_BCRYPT);

$query = "INSERT INTO `users` SET `email` = ?, `password` = ?";

$stmt = $conn->prepare($query);

$stmt->bind_param('ss', $email, $pwd);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "User was successfully registered."]);
} else {
    // print_r($conn->error);
    http_response_code(400);
    echo json_encode(["message" => "Unable to register the user."]);
}
