<?php
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

// Retrieve optional query parameters:
// 'q' for a search keyword and 'order' for the sort order (asc or desc)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

try {
    // If a search term has been provided, filter the results by title and content
    if ($search !== '') {
        $sql = "SELECT articleId, title, content, publicationDate, authorId 
                FROM Articles 
                WHERE title LIKE :search OR content LIKE :search 
                ORDER BY publicationDate $order";
        $stmt = $db->prepare($sql);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    } else
 {
        // Otherwise, retrieve all news posts sorted by publicationDate
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