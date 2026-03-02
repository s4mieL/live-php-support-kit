<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$botList = ['curl', 'wget', 'bot', 'spider', 'crawler'];

foreach($botList as $bot){
    if (stripos($userAgent, $bot) !== false){
        http_response_code(403); // block
        exit('No bots allowed');
    }
}

include __DIR__ . '/api/ip-api.php'; // change the location later to "/api/ip-api.php";
include __DIR__ . "/.database/database.php";

$unique_id = $_GET['uid'] ?? '';

if (!$unique_id) {

    header("Location: blocked.php");
    //echo "SUSPICIOUS ACTIVITY!"; // if the uid parameter is empty, return an error!
    exit;
}

$fixed_uid = urldecode($unique_id);


//$uid = new_database_checker($conn, $fixed_uid);
//mysqli_close($conn);

$uid = $unique_id; // have to retract some details sorry but im pretty sure you could still use this
if ($uid === null) {

    // if theres no uid injected, returns echos suspicious activity
    header("Location: blocked.php");
    //echo "SUSPICIOUS ACTIVITY!! AND INVALUD SESSION ID!";
    exit;
}

header('Location: index-page.php?uid=' . base64_encode(urlencode($uid)));
exit;

?>
