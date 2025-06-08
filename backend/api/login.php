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

// Read and log the raw POST data (for debugging)
// $rawData = file_get_contents('php://input');
// error_log("Raw input: " . $rawData);

// Decode the JSON payload
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Validate required fields
if (empty($data['identity']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit;
}

try {
    // Query for a user matching either the username or email
    $stmt = $db->prepare("SELECT userId, username, password, role FROM Users WHERE username = ? OR email = ?");
    $stmt->execute([$data['identity'], $data['identity']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password using password_verify (ensure registration hashes the password)
    if ($user && password_verify($data['password'], $user['password'])) {
        echo json_encode([
            'success'   => true,
            'userId'   => $user['userId'],
            'username'  => $user['username'],
            'role'      => $user['role']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }
} catch (PDOException $e) {
    error_log("Database error in login.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>