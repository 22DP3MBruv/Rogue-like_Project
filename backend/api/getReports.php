<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

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
    // Assume the reporterId is passed as a GET parameter for non-moderators.
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