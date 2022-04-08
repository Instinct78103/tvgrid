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

$email = $data->email;
$pwd = $data->pwd;

$query = "SELECT `id`, `email`, `password` FROM `users` WHERE `email` = ? LIMIT 0,1";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$num = $stmt->get_result()->fetch_assoc();

if (count($num) === 1) {

    $id = $num['id'];
    $email = $num['email'];
    $pwd2 = $num['password'];

    if (password_verify($pwd, $pwd2)) {
//    if ($pwd === $pwd2) {
        $secret_key = "SECRET";
        $issuer_claim = "tvgrid"; // this can be the servername
        $audience_claim = "AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 60; // expire time in seconds
        $token = [
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => [
                "id" => $id,
                "email" => $email,
            ],
        ];

        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        echo json_encode(
            [
                "message" => "Successful login.",
                "jwt" => $jwt,
                "email" => $email,
                "expireAt" => $expire_claim,
            ]);

    } else {
        http_response_code(401);
        echo json_encode(['message' => "Login failed"]);
    }
}