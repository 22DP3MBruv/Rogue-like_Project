<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['currentUsername']) || !isset($data['oldPassword'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$currentUsername = $data['currentUsername'];
$oldPassword = $data['oldPassword'];

try {
    $stmt = $db->prepare("SELECT password FROM Users WHERE username = :username");
    $stmt->execute([':username' => $currentUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "error" => "User not found."]);
        exit;
    }
    
    if ($user['password'] !== $oldPassword) {
        echo json_encode(["success" => false, "error" => "Incorrect password."]);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM Users WHERE username = :username");
    $stmt->execute([':username' => $currentUsername]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>