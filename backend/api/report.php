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

// For update action
if (isset($data['action']) && $data['action'] === 'update') {
    if (!isset($data['reportId']) || !isset($data['status'])) {
        echo json_encode(["success" => false, "error" => "Missing required parameters"]);
        exit;
    }
    $reportId = $data['reportId'];
    $status = $data['status'];
    if (!in_array($status, ['Open', 'Resolved'])) {
        echo json_encode(["success" => false, "error" => "Invalid status value"]);
        exit;
    }
    try {
        $stmt = $db->prepare("UPDATE Reports SET status = :status WHERE reportId = :reportId");
        $stmt->execute([':status' => $status, ':reportId' => $reportId]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
}

// For creating a new report
if (isset($data['title']) && isset($data['type']) && isset($data['content'])) {
    if (!isset($data['reporterId']) || $data['reporterId'] <= 0) {
        echo json_encode(["success" => false, "error" => "Missing or invalid reporterId"]);
        exit;
    }
    $reporterId = $data['reporterId'];
    $title      = $data['title'];
    $reportType = $data['type'];
    $content    = $data['content'];

    try {
        $stmt = $db->prepare("INSERT INTO Reports (reporterId, title, type, content, status) VALUES (:reporterId, :title, :type, :content, 'Open')");
        $stmt->execute([
          ':reporterId' => $reporterId,
          ':title'      => $title,
          ':type'       => $reportType,
          ':content'    => $content
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request"]);
?>