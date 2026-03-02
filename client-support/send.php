<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$conn = new mysqli("localhost", "root", "password", "chat-db");

$msg = $_POST['message'] ?? '';
$sender = $_POST['sender'] ?? '';
$chat_id = $_POST['chat-id'] ?? ($_SESSION['chat_id'] ?? '');

// ALWAYS sync session to JS
$_SESSION['chat_id'] = $chat_id;

// Validate
if (!$chat_id || !$msg || !$sender) {
    echo "ERROR: Missing fields";
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (chat_id, sender, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $chat_id, $sender, $msg);
$stmt->execute();

echo ($stmt->affected_rows > 0) ? "OK" : "ERROR";
