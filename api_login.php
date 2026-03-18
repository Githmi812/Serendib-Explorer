<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email    = trim($data["email"]    ?? "");
$password =      $data["password"] ?? "";

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Please fill in both fields."]);
    exit;
}

// Find user by email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user["password"])) {
    echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Welcome back, " . $user["username"] . "!",
    "token"   => "dummy-token",
    "user"    => ["id" => $user["id"], "name" => $user["username"], "email" => $user["email"]]
]);
?>