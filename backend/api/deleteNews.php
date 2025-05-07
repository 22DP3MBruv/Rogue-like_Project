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

if (!isset($data['articleId'])) {
    echo json_encode(["success" => false, "error" => "Missing articleId field"]);
    exit;
}

$articleId = $data['articleId'];

try {
    $stmt = $db->prepare("DELETE FROM Articles WHERE articleId = :articleId");
    $stmt->execute([':articleId' => $articleId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Article not found or already deleted"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>