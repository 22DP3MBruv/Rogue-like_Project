<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pending = isset($_GET['pending']) && $_GET['pending'] == 1;

if ($pending) {
    try {
        $stmt = $db->prepare("
            SELECT r.reportId, r.reporterId, r.type, r.content, r.status, r.date, u.username AS reporterName
            FROM Reports r
            JOIN Users u ON r.reporterId = u.userId
            WHERE r.status = 'Open'
            ORDER BY r.date DESC
        ");
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "reports" => $reports]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
} else {
    $reporterId = $_GET['reporterId'] ?? 0;
    try {
        $stmt = $db->prepare("SELECT reportId, type, content, status, date FROM Reports WHERE reporterId = :reporterId ORDER BY date DESC");
        $stmt->execute([':reporterId' => $reporterId]);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "reports" => $reports]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>