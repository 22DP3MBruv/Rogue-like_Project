<?php
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// Decode incoming JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Ensure required parameters are provided
if (!isset($data['currentUsername']) || !isset($data['oldPassword'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$currentUsername = $data['currentUsername'];
$oldPassword = $data['oldPassword'];

try {
    // Retrieve the current user's password from the database
    $stmt = $db->prepare("SELECT password FROM Users WHERE username = :username");
    $stmt->execute([':username' => $currentUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "error" => "User not found."]);
        exit;
    }
    
    // For production, use password_verify() with hashed passwords:
    // if (!password_verify($oldPassword, $user['password'])) { ... }
    if ($user['password'] !== $oldPassword) {
        echo json_encode(["success" => false, "error" => "Incorrect password."]);
        exit;
    }

    // Delete the user account
    $stmt = $db->prepare("DELETE FROM Users WHERE username = :username");
    $stmt->execute([':username' => $currentUsername]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>