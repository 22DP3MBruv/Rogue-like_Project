<?php
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");
    
    require_once '../config/database.php';
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'error' => 'All fields are required']);
      exit;
    }
    
    try {
      // Check if username/email already exists
      $stmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
      $stmt->execute([$data['username'], $data['email']]);
      if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Username/email already exists']);
        exit;
      }
    
      // Insert user
      $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
      $stmt = $db->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
      $stmt->execute([$data['username'], $data['email'], $password_hash]);
      
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
?>