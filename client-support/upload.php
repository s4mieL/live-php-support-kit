<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sender = isset($_POST['sender']) ? $_POST['sender'] : 'unknown';
$chat_id = isset($_POST["chat-id"]) ? $_POST["chat-id"] : "";

if (empty($chat_id)) {
    echo "invalid: chat id";
    exit;
}

$conn = new mysqli("localhost", "root", "password", "chat-db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$targetDir = "uploads/";
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

if (!empty($_FILES['file']['name'])) {
    $fileName = time() . "_" . basename($_FILES['file']['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO messages (chat_id, sender, message) VALUES (?, ?, ?)");
        $sender = $sender; // or dynamically set
        $message = "[file]" . $fileName;
        $stmt->bind_param("sss", $chat_id, $sender, $message);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "ok", "file" => $fileName]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to move uploaded file"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No file uploaded"]);
}
?>
