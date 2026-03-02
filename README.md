# PHP Live Chat & Access Monitor Templates

**Author:** s4miEL  
**Stack:** PHP, MySQL, JavaScript, Discord Webhooks

---

## Overview

A collection of lightweight, self-hosted PHP templates for adding live customer support chat and access monitoring to any website. No external SaaS required — just a server, a MySQL database, and a Discord webhook.

---

## File Structure

```
/
├── api/
│   ├── index.php           # Entry point with IP logging + redirect
│   ├── ip-api.php          # Standalone IP logger for restricted areas
│   └── .htaccess           # Access control for the api folder
│
├── client-chat/
│   ├── live-chat.php       # Client-facing chat widget
│   ├── agent-chat.php      # Agent-facing chat interface
│   ├── send.php            # Handles sending messages
│   ├── fetch.php           # Polls and returns chat messages
│   └── upload.php          # Handles file/image uploads
│
└── .database/
    └── database.php        # Auto-setup database connection
```

---

## Requirements

- PHP 7.4 or higher
- MySQL / MariaDB
- cURL enabled (`php-curl`)
- A Discord server with a webhook URL

---

## Setup

### 1. Database

Drop `database.php` into a `.database/` folder at your project root. It will automatically create the `chat-db` database and `messages` table on first run — no manual SQL needed.

Open `database.php` and set your credentials:

```php
$host = "localhost";
$user = "your_db_user";
$pass = "your_db_password";
$dbname = "chat-db";
```

---

### 2. Live Chat

**Client side — `live-chat.php`**

Include or embed this file on any page where you want the chat widget to appear. A floating 💬 **Live Support** button will show up in the bottom-right corner and auto-open after 2 seconds.

Open the file and set your Discord webhook inside `send_notifier()`:

```php
$webhookurl = "https://discord.com/api/webhooks/your/webhook";
```

When a new visitor opens the chat, your Discord channel will receive a notification with a direct link to the agent panel.

---

**Agent side — `agent-chat.php`**

This is the interface your support agents use to respond to clients. Access it via the link sent to Discord:

```
https://yourdomain.com/agent-chat.php?chat-id=CHAT_ID_HERE
```

Agents will be prompted to enter their **agent code** before accessing the chat. Once verified, they can send messages, upload files, and use masked links.

**Masked link format (agent use only):**
```
[link]https://example.com/|Click here
```
This renders as a clickable hyperlink on the client's side.

---

### 3. Message Polling

`fetch.php` and `send.php` are backend-only files — do not link to them directly. They are called automatically by the chat widget via JavaScript every few seconds.

- Client messages poll every **3 seconds**
- Agent messages poll every **1 second**

---

### 4. File Uploads

`upload.php` handles attachments sent through the paperclip 📎 button. Uploaded files are saved to an `uploads/` folder (auto-created if missing) and logged to the database.

Supported types: images (JPG, PNG, GIF) render inline; all other files appear as a download link.

---

### 5. Access Monitor

**`index.php`** — Place this at any entry point you want to monitor. It silently logs the visitor's IP, user agent, and geolocation to your Discord webhook, then redirects them to your target URL.

Set your webhook and redirect destination:

```php
$webhook = "https://discord.com/api/webhooks/your/webhook";

header("Location: https://www.yoursite.com/");
```

---

**`ip-api.php`** — A standalone version intended for restricted paths (e.g., admin directories, webshells). Include it at the top of any sensitive file:

```php
include __DIR__ . '/ip-api.php';
```

Set your webhook:

```php
$webhook = "https://discord.com/api/webhooks/your/webhook";
```

---

## Discord Webhook Setup

1. Open your Discord server
2. Go to **Server Settings → Integrations → Webhooks**
3. Click **New Webhook**, choose a channel, and copy the URL
4. Paste it into the relevant `$webhook` variable in each file

---

## Notes

- The `chat-id` in the URL ties the client and agent to the same conversation thread. Do not modify it manually.
- Agent codes are currently free-form — you can extend `agent-chat.php` to validate codes against a database for stricter access control.
- Make sure your server has write permissions on the `uploads/` directory.

---

## License

Free to use and modify for personal and commercial projects. Credit appreciated but not required.