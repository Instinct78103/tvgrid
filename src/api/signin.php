<?php

require_once 'DatabaseService.php';
require "../vendor/autoload.php";

use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$email = '';
$pwd = '';

$dbService = new DatabaseService();
$conn = $dbService->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (property_exists($data, 'email')) {
    $email = $data->email;
}

if (property_exists($data, 'password')) {
    $pwd = $data->password;
}

$query = "SELECT `id`, `email`, `password` FROM `users` WHERE `email` = ? LIMIT 0,1";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$num = $stmt->get_result()->fetch_assoc() or exit(json_encode(['message' => "Login failed"]));

if (count($num) === 3) {
    $id = $num['id'];
    $email = $num['email'];
    $pwd2 = $num['password'];

    if (password_verify($pwd, $pwd2)) {
        $secret_key = "SECRET";
        $issued_at = new DateTimeImmutable();
        $expire = $issued_at->modify('+6 minutes')->getTimestamp();
        $server_name = "tvgrid";

        $data = [
            'iat' => $issued_at->getTimestamp(),
            'iss' => $server_name,
            'nbf' => $issued_at->getTimestamp(),
            'exp' => $expire,
            'userName' => $email
        ];

        http_response_code(200);

        $jwt = JWT::encode($data, $secret_key, 'HS512');

        echo json_encode(
            [
                "message" => "Success!",
                "jwt" => $jwt,
                "email" => $email,
                "expireAt" => $expire,
            ]
        );
    } else {
        http_response_code(401);
        echo json_encode(['message' => "Login failed"]);
    }
}
