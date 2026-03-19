<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "function.php";
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name     = sanitize_text($data["name"]     ?? "");
$email    = sanitize_text($data["email"]    ?? "");
$password = sanitize_text($data["password"] ?? "");

if (!$name || !$email || !$password) {
    json_response(false, "All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(false, "All fields are required.");
}

if (strlen($password) < 6) {
    json_response(false, "All fields are required.");
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_response(false, "All fields are required.");
}

// Save the user (password is hashed for security)
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $hashed]);

echo json_encode(["success" => true, "message" => "Account created successfully!"]);
?>
