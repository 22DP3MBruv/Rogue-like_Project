<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['currentUsername']) || empty($data['oldPassword']) || empty($data['newPassword'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$currentUsername = $data['currentUsername'];
$oldPassword     = $data['oldPassword'];
$newPassword     = $data['newPassword'];

try {
    $stmt = $db->prepare("SELECT password FROM Users WHERE username = :username");
    $stmt->execute([':username' => $currentUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "User not found."]);
        exit;
    }

    if ($user['password'] !== $oldPassword) {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Incorrect current password."]);
        exit;
    }

    $stmt2 = $db->prepare("UPDATE Users SET password = :newPassword WHERE username = :username");
    $result = $stmt2->execute([':newPassword' => $newPassword, ':username' => $currentUsername]);

    echo json_encode(["success" => $result]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>