<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

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
        // Update username, email, and password
        $stmt = $db->prepare("UPDATE Users SET username = :username, email = :email, password = :password WHERE username = :currentUsername");
        $result = $stmt->execute([
            ':username'        => $newUsername,
            ':email'           => $email,
            ':password'        => $password,  // For production use password hashing
            ':currentUsername' => $currentUsername
        ]);
    } else {
        // Update username and email only
        $stmt = $db->prepare("UPDATE Users SET username = :username, email = :email WHERE username = :currentUsername");
        $result = $stmt->execute([
            ':username'        => $newUsername,
            ':email'           => $email,
            ':currentUsername' => $currentUsername
        ]);
    }

    if ($result) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Account update failed"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>