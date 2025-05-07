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

if (!isset($data['reportId'])) {
    echo json_encode(["success" => false, "error" => "Missing reportId field"]);
    exit;
}

$reportId = $data['reportId'];

try {
    $stmt = $db->prepare("DELETE FROM Reports WHERE reportId = :reportId");
    $stmt->execute([':reportId' => $reportId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Report not found or already deleted"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>