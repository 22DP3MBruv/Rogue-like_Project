<?php
require_once 'config/database.php';

try {
  // Create the `users` table
  if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    die("Access denied.");
  }

  $sql = "
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) NOT NULL UNIQUE,
      email VARCHAR(100) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  ";

  $db->exec($sql);
  echo "Tables created successfully! 🎉";
} catch (PDOException $e) {
  die("Error creating tables: " . $e->getMessage());
}
?>