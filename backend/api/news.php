<?php
ini_set('display_errors', 0);
error_reporting(0);

// Start output buffering
ob_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';  

if (!function_exists('send_json_response')) {
    function send_json_response($data, $status = 200) {
        if (ob_get_length()) {
            ob_end_clean(); // End and clean any buffered output
        }
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$order  = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

// Pagination parameters: default limit = 10, page = 1
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

try {
    // Build count query to calculate total pages
    if ($search !== '') {
        $countSql = "SELECT COUNT(*) AS total FROM Articles WHERE MATCH(title, content) AGAINST(:search IN BOOLEAN MODE)";
        $countStmt = $db->prepare($countSql);
        $searchTerm = $search;
        $countStmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    } else {
        $countSql = "SELECT COUNT(*) AS total FROM Articles";
        $countStmt = $db->prepare($countSql);
    }
    $countStmt->execute();
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalArticles = $countResult ? (int)$countResult['total'] : 0;
    $totalPages = ceil($totalArticles / $limit);

    // Build main query with limit & offset
    if ($search !== '') {
        $sql = "SELECT articleId, title, content, publicationDate, authorId 
                FROM Articles 
                WHERE MATCH(title, content) AGAINST(:search IN BOOLEAN MODE)
                ORDER BY publicationDate $order
                LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        $searchTerm = $search;
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    } else {
        $sql = "SELECT articleId, title, content, publicationDate, authorId 
                FROM Articles 
                ORDER BY publicationDate $order
                LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
    }
    // Bind limit and offset as integers
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($articles === false) {
        throw new PDOException("Error fetching articles: " . implode(":", $db->errorInfo()));
    }
    
    send_json_response([
        'success'    => true,
        'articles'   => $articles,
        'totalPages' => $totalPages
    ]);
} catch (PDOException $e) {
    error_log("News API error: " . $e->getMessage());
    send_json_response(['success' => false, 'error' => 'Database error.'], 500);
}
?>