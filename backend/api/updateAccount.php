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

if (
    !isset($data['currentUsername']) ||
    !isset($data['username']) ||
    !isset($data['oldPassword'])
) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$currentUsername = $data['currentUsername'];
$newUsername     = $data['username'];
$oldPassword     = $data['oldPassword'];

try {
    // Verify that the user exists and retrieve the hashed password
    $stmt = $db->prepare("SELECT password FROM Users WHERE username = :currentUsername");
    $stmt->execute([':currentUsername' => $currentUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(["success" => false, "error" => "User not found"]);
        exit;
    }
    
    // Only proceed if the old password is valid
    if (!password_verify($oldPassword, $user['password'])) {
        echo json_encode(["success" => false, "error" => "Invalid old password"]);
        exit;
    }
    
    $stmt = $db->prepare("UPDATE Users SET username = :username WHERE username = :currentUsername");
    $result = $stmt->execute([
        ':username' => $newUsername,
        ':currentUsername' => $currentUsername
    ]);

    echo json_encode(["success" => $result]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>