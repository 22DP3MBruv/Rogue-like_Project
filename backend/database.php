<?php
    $host = 'localhost';
    $dbname = 'gamedb';
    // Placeholders for database username and password
    $user = 'root';
    $pass = 'passkey';
    
    try {
      $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die("Database connection failed: " . $e->getMessage());
    }
?>