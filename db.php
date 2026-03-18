<?php
$host     = "localhost";
$dbname   = "serendib_db";
$username = "root";
$password = "";   // XAMPP default has no password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}
?>