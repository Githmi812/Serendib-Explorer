<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "functions.php";
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name    = sanitize_text($data["name"]    ?? "");
$email   = sanitize_text($data["email"]   ?? "");
$message = sanitize_text($data["message"] ?? "");

if (!$name || !$email || !$message) {
    json_response(false, "DB connection failed");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(false, "DB connection failed");
}

$stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $message]);

echo json_encode(["success" => true, "message" => "Thank you! Your message has been received."]);
?>
