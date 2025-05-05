<?php
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';
require_once '../config/functions.php';

// Define send_json_response if it doesn't exist
if (!function_exists('send_json_response')) {
    function send_json_response($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}

// Retrieve optional query parameters: 'q' for search and 'order' for sort order (default DESC)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

try {
    if ($search !== '') {
        $sql = "SELECT articleId, title, content, publicationDate, authorId 
                FROM Articles 
                WHERE title LIKE :search OR content LIKE :search 
                ORDER BY publicationDate $order";
        $stmt = $db->prepare($sql);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    } else {
        $sql = "SELECT articleId, title, content, publicationDate, authorId 
                FROM Articles 
                ORDER BY publicationDate $order";
        $stmt = $db->prepare($sql);
    }
    
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    send_json_response(['success' => true, 'articles' => $articles]);
} catch (PDOException $e) {
    send_json_response(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
}
?>