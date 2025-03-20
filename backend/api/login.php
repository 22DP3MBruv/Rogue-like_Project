<?php
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *"); // Allow requests from your Vue/Godot frontend
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");
    
    require_once '../config/database.php';
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($data['username']) || empty($data['password'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'error' => 'Missing fields']);
      exit;
    }
    
    try {
      $stmt = $db->prepare('SELECT userId, password, role FROM Users WHERE username = ?');
      $stmt->execute([$data['username']]);
      $user = $stmt->fetch();
    
      if ($user && password_verify($data['password'], $user['password'])) {
        // Return role along with userId if login is successful
        echo json_encode([
          'success' => true,
          'user_id' => $user['userId'],
          'role' => $user['role']
        ]);
      } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
      }
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'Database error']);
    }
?>