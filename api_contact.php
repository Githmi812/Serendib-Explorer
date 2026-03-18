<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name    = trim($data["name"]    ?? "");
$email   = trim($data["email"]   ?? "");
$message = trim($data["message"] ?? "");

if (!$name || !$email || !$message) {
    echo json_encode(["success" => false, "message" => "Please fill in all fields."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $message]);

echo json_encode(["success" => true, "message" => "Thank you! Your message has been received."]);
?>
