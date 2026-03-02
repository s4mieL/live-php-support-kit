<?php

$host = "localhost";
$user = "root";
$pass = "password";
$dbname = "chat-db";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

$conn->select_db($dbname);

$conn->query("
    CREATE TABLE IF NOT EXISTS `messages` (
        `id`        INT AUTO_INCREMENT PRIMARY KEY,
        `chat_id`   VARCHAR(64)  NOT NULL,
        `sender`    VARCHAR(32)  NOT NULL,
        `message`   TEXT         NOT NULL,
        `created_at` TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
