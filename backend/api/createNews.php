<?php
ini_set('display_errors', 0);
error_reporting(0);

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

// Validate required fields: title, content, and authorId
if (
    !isset($data['title']) || empty(trim($data['title'])) ||
    !isset($data['content']) || empty(trim($data['content'])) ||
    !isset($data['authorId']) || empty(trim($data['authorId']))
) {
    echo json_encode(["success" => false, "error" => "Missing required fields."]);
    exit;
}

$title    = trim($data['title']);
$content  = trim($data['content']);
$authorId = intval($data['authorId']); // Logged in user id

try {
    $stmt = $db->prepare("INSERT INTO Articles (title, content, publicationDate, authorId) VALUES (:title, :content, NOW(), :authorId)");
    $result = $stmt->execute([
        ':title'    => $title,
        ':content'  => $content,
        ':authorId' => $authorId
    ]);
    
    if ($result) {
        echo json_encode(["success" => true, "message" => "News created successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to create news."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error: " . $e->getMessage()]);
}
?>