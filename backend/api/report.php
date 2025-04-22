<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Check if this is an update request
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

// If not update then assume it is a new report submission.
if (isset($data['type']) && isset($data['content'])) {
    $reportType = $data['type'];
    $content = $data['content'];
    // In a real application, reporterId would come from the user’s session/authentication context.
    $reporterId = $data['reporterId'] ?? 0;
    try {
        $stmt = $db->prepare("INSERT INTO Reports (reporterId, type, content, status) VALUES (:reporterId, :type, :content, 'Open')");
        $stmt->execute([':reporterId' => $reporterId, ':type' => $reportType, ':content' => $content]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request"]);
?>