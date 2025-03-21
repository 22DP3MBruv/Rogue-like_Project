<?php
require_once 'config/database.php';

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define Users table
$sql_users = "CREATE TABLE IF NOT EXISTS Users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
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

// Define Articles table
$sql_articles = "CREATE TABLE IF NOT EXISTS Articles (
    articleId INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    publicationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    authorId INT NOT NULL,
    CONSTRAINT FK_Author FOREIGN KEY (authorId) REFERENCES Users (userId)
);";

// Execute table creation queries
if ($conn->query($sql_users) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating Users table: " . $conn->error . "<br>";
}

if ($conn->query($sql_reports) === TRUE) {
    echo "Reports table created successfully<br>";
} else {
    echo "Error creating Reports table: " . $conn->error . "<br>";
}

if ($conn->query($sql_articles) === TRUE) {
    echo "Articles table created successfully<br>";
} else {
    echo "Error creating Articles table: " . $conn->error . "<br>";
}

$conn->close();
?>