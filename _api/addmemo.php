<?php
header("Content-Type: application/json");

// Response helper
function respond($success, $message) {
    echo json_encode(["success" => $success, "message" => $message]);
    exit;
}

// Validate fields
if (
    !isset($_POST['title']) ||
    !isset($_POST['message']) ||
    !isset($_POST['key'])
) {
    respond(false, "Missing required fields.");
}

$title = trim($_POST['title']);
$message = trim($_POST['message']);
$key = trim($_POST['key']);

// Check access key
if ($key !== "strong_passwird") {
    respond(false, "Invalid access key.");
}

$file = __DIR__ . "/appdata.json";

// Read or initialize
$data = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    if ($json !== false && !empty($json)) {
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
}

// Create new memo
$now = new DateTime();
$expiry = (clone $now)->modify("+3 days");

$newMemo = [
    "name" => $title,
    "data" => $message,
    "datecreated" => $now->format("Y-m-d H:i:s"),
    "expiry" => $expiry->format("Y-m-d H:i:s")
];

// Append and save
$data[] = $newMemo;
if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) === false) {
    respond(false, "Failed to save memo.");
}

respond(true, "Memo saved successfully.");
