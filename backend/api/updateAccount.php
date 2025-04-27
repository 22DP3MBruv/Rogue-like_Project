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

if (
    !isset($data['currentUsername']) ||
    !isset($data['username']) ||
    !isset($data['email'])
) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$currentUsername = $data['currentUsername'];
$newUsername     = $data['username'];
$email           = $data['email'];
$password        = isset($data['password']) ? trim($data['password']) : '';

try {
    if ($password !== '') {
        $stmt = $db->prepare("UPDATE Users SET username = :username, email = :email, password = :password WHERE username = :currentUsername");
        $result = $stmt->execute([
            ':username'        => $newUsername,
            ':email'           => $email,
            ':password'        => $password,
            ':currentUsername' => $currentUsername
        ]);
    } else {
        $stmt = $db->prepare("UPDATE Users SET username = :username, email = :email WHERE username = :currentUsername");
        $result = $stmt->execute([
            ':username'        => $newUsername,
            ':email'           => $email,
            ':currentUsername' => $currentUsername
        ]);
    }

    echo json_encode(["success" => $result]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>