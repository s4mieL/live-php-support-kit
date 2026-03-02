<?php
session_start();

$chat_id = $_GET["chat-id"] ?? "";
$agent_code = $_GET["agent_code"] ?? "";

if (!isset($chat_id) || $chat_id == "") {
    header("location: https://mydomain.com/");
    exit;
}

if (!empty($chat_id) && !empty($agent_code)) {
    //send discord notifier to let supervisors know
    //this client is already being handled
}

// If POST has employee code → save it
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["agent_code"])) {
    $agent_code_post = $_POST["agent_code"];

    // OPTIONAL: Add logic to mark this chat as "assigned" in your DB
    // assignChatToAgent($chat_id, $_SESSION["agent_code"]);

    header("Location: agent-chat.php?chat-id=" . $chat_id . "&agent_code=" . $agent_code_post);
    exit;
}

// If the agent has NOT entered a code yet → show the form
elseif (empty($agent_code)) {
    ?>
    <!DOCTYPE html>
    <html>
   <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <title>Agent Verification | s4miEL</title>
        <link rel="icon" href="../www/img/favicon_0.png" type="image/x-icon">
        <style type="text/css">

            /* Subtle autofill-like highlight */
            .highlighteds {
                animation: highlight 1.5s forwards;
            }

            @keyframes highlight {
                0%   { background-color: #fff9c4; } /* pale yellow */
                100% { background-color: white; }
            }

            .highlighted {
                font-weight: bold;
                background-color: yellow;
                padding: 5px;
            }

            .utility-bar {
                background: #0b1e5b;
                height: 34px;                
            }

            .utility-inner {
                max-width: 1200px;
                margin: 0 auto;
                height: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 16px;
            }

            .utility-bar a {
                color: #fff;
                font-size: 12px;
                text-decoration: none;
                margin-left: 14px;
                white-space: nowrap;
            }

            .utility-bar .left a {
                opacity: 0.8;
            }

        </style>
    </head>
    <body>
<!-- MAIN HEADER -->
    <header>
        <div class="container">
            <div class="header_img"></div>
            <div style="text-align: center; padding: 0 20px;">
                <h1 style="display: flex; justify-content: space-between;">
                    <span>Chat Agent Verification</span>
                <hr>
            </div>

            <div class="content" style="display: flex; gap: 32px;">
                <div style="flex: 1; padding: 0 24px;">
                    <form method="POST">
                        <label for="employee code">Enter Agent Code</label><br>
                        <input placeholder="employee code" type="text" id="cell" name="agent_code" class="highlighted" value="" style="width: 100%;"><br><br>
                        <label for="address" style="opacity: 0;">Update Information</label>
                        <button class="submit_btn" type="submit">Submit</button>
                    </form>
                </div>
            </div>
            <br>
        </body>
    </html>
    <?php
    exit; // Stop rest of page until agent enters code
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Globe Telecom | Agent Side</title>
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
    <br><br>
        <div class="container" style="margin: 20px">
            <div class="header_img"></div>
            <div style="text-align: center; padding: 0 20px;">
                <h1 style="display: flex; justify-content: space-between;">
                    <span>Agent Side!</span>
                </h1>
            </div>

            <div class="content" style="display: flex; gap: 32px;">
                <div style="flex: 1; padding: 0 24px;">             
                    <h4>
                        <span style="color: #2e3c8bff ;">Tutorial for Agent Side!</span>
                    </h4>
                    <u>
                        <strong>
                            <span style="font-size:16px;">Main Trunklines</span>
                         </strong>
                    </u>
                    <br>
                    <span style="font-style:16px;">
                        <br>
                        Available: Monday to Friday, 8:00 A.M. – 5:00 P.M.
                        <br><br>
                            <p>
                                To use our support platform properly, <br>
                                always remember that <b><i>masked links</i></b> must be sent in the correct format. When sharing a masked <b>URL,</b> <br>
                                make sure it follows this exact structure: <strong>[link]<span style="color: blue;"><i>https://example.com/</i></span>|</strong><b>here.</b> and the output would be <a href="https://example.com/">here.</a> <br>
                                The masked link must be placed on its own line, separate from any other text or message. <br>
                                Mixing it with your sentence will cause a system bug and prevent the link from displaying correctly.<br><br>
                                
                                For file handling, use the paperclip icon to upload attachments—this ensures all files are delivered safely and logged properly in our system.
                                When assisting a client who needs support, always ask for their reference ticket number along with their full name. These two details allow you to quickly verify whether their information exists in our system or database. If the reference ticket or client record does not appear in the system, notify the system administrator immediately so the issue can be checked and resolved. And above all, maintain professionalism: speak politely, stay respectful, and ensure every client receives clear and courteous assistance.
                            </p>
  
                    </span>
                    <br>
                    
                </div>
            </div><br><br>

    <script>
        window.onload = function() {
            setTimeout(function() {
                
                toggleChat();
            }, 5000); 
        };

    </script>


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
            <div class="msg incoming">Hello agent! we're redirecting you to a client.. goodluck.. -ac1x</div>
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
        formData.append("sender", "admin");
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
                div.className = "msg " + (m.sender === "admin" ? "outgoing" : "incoming");

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
    setInterval(loadMessages, 1000);

    // Send message
    function sendMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        const chatID = urlParams.get("chat-id");

        const msg = document.getElementById("msgInput").value.trim();
        if (!msg) return;

        const form = new FormData();
        form.append("message", msg);
        form.append("sender", "admin");
        form.append("chat-id", chatID);

        fetch("send.php", { method:"POST", body:form })
        .then(() => document.getElementById("msgInput").value = "");
    }

    </script>
</body>
</html>
