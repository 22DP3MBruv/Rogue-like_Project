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

if (!isset($data['articleId'], $data['title'], $data['content'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$articleId = $data['articleId'];
$title = trim($data['title']);
$content = trim($data['content']);

try {
    $stmt = $db->prepare("UPDATE Articles SET title = :title, content = :content WHERE articleId = :articleId");
    $stmt->execute([
        ':title' => $title,
        ':content' => $content,
        ':articleId' => $articleId
    ]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>