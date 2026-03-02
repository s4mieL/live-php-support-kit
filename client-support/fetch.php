<?php
session_start();
$conn = new mysqli("localhost", "root", "password", "chat-db");

$chat_id = $_SESSION['chat_id'] ?? '';

$stmt = $conn->prepare("SELECT * FROM messages WHERE chat_id = ? ORDER BY id ASC");
$stmt->bind_param("s", $chat_id);
$stmt->execute();

$result = $stmt->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {

    $message = $row['message'];
    $type = "text";
    $file_url = null;

    if (strpos($message, "[file]") === 0) {
        $filename = substr($message, 6);
        $file_url = "uploads/" . $filename;

        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $filename)) {
            $type = "image";
        } else {
            $type = "file";
        }

        $message = "";
    }

    $messages[] = [
        "id" => $row["id"],
        "sender" => $row["sender"],
        "message" => $message,
        "type" => $type,
        "file_url" => $file_url
    ];
}

header("Content-Type: application/json");
echo json_encode($messages);
