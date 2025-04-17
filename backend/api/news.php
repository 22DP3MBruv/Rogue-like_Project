<?php
// filepath: c:\Users\marks\Documents\Personal\Rogue-like_Project\Rogue-like_Project\backend\api\news.php

// Set headers for JSON output and enable GET requests
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

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
    } else {
        // Otherwise, retrieve all news posts sorted by publicationDate
        $sql = "SELECT articleId, title, content, publicationDate, authorId 
                FROM Articles 
                ORDER BY publicationDate $order";
        $stmt = $db->prepare($sql);
    }
    
    $stmt->execute();
    // Fetch all results as an associative array
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the list of articles as a JSON response
    send_json_response(['success' => true, 'articles' => $articles]);
    
} catch (PDOException $e) {
    // Return a JSON error response if a database error occurs
    send_json_response(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
}
?>