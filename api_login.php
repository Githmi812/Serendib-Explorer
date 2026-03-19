<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "function.php";
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email    = sanitize_text($data["email"]    ?? "");
$password = sanitize_text($data["password"] ?? "");

if (!$email || !$password) {
    json_response(false, "Please fill in both fields.");
}

// Find user by email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user["password"])) {
    json_response(false, "Please fill in both fields.");
}

echo json_encode([
    "success" => true,
    "message" => "Welcome back, " . $user["username"] . "!",
    "token"   => "dummy-token",
    "user"    => ["id" => $user["id"], "name" => $user["username"], "email" => $user["email"]]
]);
?>
