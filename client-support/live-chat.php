<?php

session_start();

if (isset($_GET['chat-id']) || isset($_SESSION['chat-id'])) {
    $chatid = $_SESSION['chat_id'] = $_GET['chat-id'];  // use URL chat-id

} elseif (!isset($_SESSION['chat_id'])) {
    $chatid = bin2hex(random_bytes(16)); // fallback create new
    $_SESSION['chat_id'] = $chat_id;
    send_notifier($chatid);
    header("location: ?chat-id=" . $chatid);
    exit;
}

$chat_id = $_SESSION['chat_id'];

function send_notifier($chatid) {
    $webhookurl = "your webhook here for notifier";

    $message = "||@everyone|| A Client Requesting for Assistance!!\n https://example.com/client-support/agent-chat.php?chat-id=" . $chatid;

    $data = array(
        "content" => $message,
        "username" => "Client Notifier!",
        "avatar_url" => "your-logo.jpeg"
    );

    $json_data = json_encode($data, JSON_UNESCAPED_SLASHES);

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);

    curl_close($ch);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Support Chat Template | s4mieL</title>
    <link rel="shortcut icon" type="image/x-icon" href="../www/img/favicon_0.png">
    <style>

        body, html {
            overflow-x: hidden;
        }

        hr {
            border-bottom: 1px solid #d7df23;
            height: -10px;
        }

        html, body {
            height: 100%;   
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #FAFAFA;
            display: flex;
            flex-direction: column;

            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        header {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 100;
        }


        header a {
        color: #fff;
        margin-right: 20px;
        text-decoration: none;
        }
        section {
        padding: 100px 20px;
        min-height: 100vh;
        }

        /* Floating Button */
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #2e3c8bff;
            color: #fff;
            padding: 14px 22px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: 0.2s;
            z-index: 10000;
        }

        .chat-button:hover {
            transform: scale(1.05);
        }

        .chat-window {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 380px;
            height: 520px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.25);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 10000;
        }

        .chat-header {
            background: #2e448bff;
            padding: 16px;
            color: white;
            font-size: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header span {
            font-weight: bold;
        }

        .close-btn {
            font-size: 20px;
            cursor: pointer;
        }

        .messages {
            position: relative; /* ✅ enables z-index */
            flex: 1;
            padding: 15px;
            background: #f5f4ff;
            overflow-y: auto;
            z-index: 9999;
        }


        .msg {
            margin-bottom: 12px;
            padding: 12px 16px;
            max-width: 80%;
            border-radius: 12px;
            font-size: 14px;
        }

        .incoming { background: #dfe6f6ff; }
        .outgoing { background: #2e38ccff; color: white; margin-left: auto; }

        .input-area {
            display: flex;
            padding: 10px;
            background: #ffffff;
        }

        .input-area input {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #b5b6e6ff;
        }

        .input-area button {
            margin-left: 10px;
            padding: 10px 20px;
            background: #302e8bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

    </style>

</head>
<body>
    <div class="chat-button" onclick="toggleChat()">
        💬 Live Support
    </div>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <span>Live Support</span>
            <small>• Online</small>
            <div class="close-btn" onclick="toggleChat()">✖</div>
        </div>

        <div class="messages" id="messages">
            <div class="msg incoming">Hello! How can we help you today? you will be redirected to an agent please standby. Thank you for your patience 😊</div>
        </div>
        <!-- Hidden file input -->
        <input type="file" id="fileInput" style="display:none;" />

        <div class="input-area">
            <input type="text" id="msgInput" placeholder="Type your message..." />
             <!-- Custom file button -->
            <button id="fileBtn" style="background:#f0f0f0; color:#333; border:none; border-radius:8px; padding:10px; cursor:pointer; font-weight:bold;">
                📎
            </button>
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                
                toggleChat();
            }, 2000); 
        };

    </script>

    <script>

    // Toggle chat window
    function toggleChat() {
        const chat = document.getElementById('chatWindow');
        chat.style.display = chat.style.display === 'flex' ? 'none' : 'flex';
    }

    // File upload button
    document.getElementById('fileBtn').onclick = function() {
        document.getElementById('fileInput').click();
    }

    // Handle file selection
    document.getElementById('fileInput').onchange = function() {
        const file = this.files[0];
        if (!file) return;

        const urlParams = new URLSearchParams(window.location.search);
        const chatID = urlParams.get("chat-id");

        const formData = new FormData();
        formData.append("file", file);
        formData.append("sender", "user");
        formData.append("chat-id", chatID);

        fetch("upload.php", { method: "POST", body: formData })
            .then(res => res.json())
            .then(data => {
                console.log(data); // debug output
                if (data.status === "ok") {
                    sendMessage("[file]" + data.file);
                } else {
                    alert("Upload failed: " + (data.message || "unknown error"));
                }
            });
    }

    // Load messages
    function loadMessages() {
        fetch("fetch.php")
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById("messages");
            box.innerHTML = "";

            data.forEach(m => {
                const div = document.createElement("div");
                div.className = "msg " + (m.sender === "user" ? "outgoing" : "incoming");

                if (m.type === "image") {
                    const img = document.createElement("img");
                    img.src = m.file_url;
                    img.style.maxWidth = "100%";
                    img.style.borderRadius = "8px";
                    div.appendChild(img);
                } else if (m.type === "file") {
                    const link = document.createElement("a");
                    link.href = m.file_url;
                    link.download = m.file_url.split("/").pop();
                    link.innerText = "Download File";
                    div.appendChild(link);
                } 

                else if (m.message.startsWith("[link]")) {
                    const content = m.message.slice(6); // removes "[link]" completely
                    const separatorIndex = content.indexOf("|");

                    let url, text;

                    if (separatorIndex !== -1) {
                        url = content.substring(0, separatorIndex).trim();
                        text = content.substring(separatorIndex + 1).trim();
                    } else {
                        url = content.trim();
                        text = content.trim(); // fallback if no custom text
                    }

                    const a = document.createElement("a");
                    a.href = url;        // only the URL, no [link] tag
                    a.innerText = text;  // display text
                    a.target = "_blank";
                    div.appendChild(a);
                }
                
                else {
                    div.innerText = m.message;
                }

                box.appendChild(div);
            });

            box.scrollTop = box.scrollHeight;
        });
    }
    setInterval(loadMessages, 3000);

    // Send message
    function sendMessage() {

        //const urlParams = new URLSearchParams(window.location.search);
        //const chatID = urlParams.get("chat-id");

        const msg = document.getElementById("msgInput").value.trim();
        if (!msg) return;

        const form = new FormData();
        form.append("message", msg);
        form.append("sender", "user");
        form.append("chat-id", "<?php echo $chatid; ?>");

        fetch("send.php", { method:"POST", body:form })
        .then(() => document.getElementById("msgInput").value = "");
    }

    </script>
</body>
</html>
