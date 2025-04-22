<?php
require_once __DIR__ . '/database.php';

// Define Users table
$sql_users = "CREATE TABLE IF NOT EXISTS Users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Player', 'Moderator') NOT NULL
);";

// Define Reports table
$sql_reports = "CREATE TABLE IF NOT EXISTS Reports (
    reportId INT AUTO_INCREMENT PRIMARY KEY,
    reporterId INT NOT NULL,
    type ENUM('GameIssue', 'WebsiteIssue') NOT NULL,
    content TEXT NOT NULL,
    status ENUM('Open', 'Resolved') NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FK_Reporter FOREIGN KEY (reporterId) REFERENCES Users (userId)
);";

// Define Articles table with a FULLTEXT index for fast searching by title and content.
// This table is used for news posts with sorting by publicationDate and keyword searches.
$sql_articles = "CREATE TABLE IF NOT EXISTS Articles (
    articleId INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    publicationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    authorId INT NOT NULL,
    CONSTRAINT FK_Author FOREIGN KEY (authorId) REFERENCES Users (userId),
    FULLTEXT INDEX ft_search (title, content)
);";

try {
    $db->exec($sql_users);
    echo "Users table created successfully<br>";
} catch (PDOException $e) {
    echo "Error creating Users table: " . $e->getMessage() . "<br>";
}

try {
    $db->exec($sql_reports);
    echo "Reports table created successfully<br>";
} catch (PDOException $e) {
    echo "Error creating Reports table: " . $e->getMessage() . "<br>";
}

try {
    $db->exec($sql_articles);
    echo "Articles table created successfully<br>";
} catch (PDOException $e) {
    echo "Error creating Articles table: " . $e->getMessage() . "<br>";
}
?>