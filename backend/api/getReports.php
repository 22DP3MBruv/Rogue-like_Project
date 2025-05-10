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

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchSQL = "";
if ($search !== "") {
    $searchSQL = " AND MATCH(r.title, r.content) AGAINST(:search IN BOOLEAN MODE)";
}

$order = "DESC";
if (isset($_GET['order']) && strtolower($_GET['order']) === "asc") {
    $order = "ASC";
}

$pending = isset($_GET['pending']) && $_GET['pending'] == 1;

if ($pending) {
    try {
        $sql = "
            SELECT r.reportId, r.reporterId, r.title, r.type, r.content, r.status, r.date, u.username AS reporterName
            FROM Reports r
            JOIN Users u ON r.reporterId = u.userId
            WHERE r.status = 'Open' $searchSQL
            ORDER BY r.date $order
        ";
        $stmt = $db->prepare($sql);
        if ($search !== "") {
            $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        }
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "reports" => $reports]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
} else if (isset($_GET['all']) && $_GET['all'] == 1) {
    try {
        $sql = "
            SELECT r.reportId, r.reporterId, r.title, r.type, r.content, r.status, r.date, u.username AS reporterName
            FROM Reports r
            JOIN Users u ON r.reporterId = u.userId
            WHERE r.status = 'Open' $searchSQL
            ORDER BY r.date $order
        ";
        $stmt = $db->prepare($sql);
        if ($search !== "") {
            $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        }
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "reports" => $reports]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;
}
?>