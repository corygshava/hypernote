<?php
header("Content-Type: application/json");

$file = __DIR__ . "/appdata.json";

// If file doesn’t exist or is empty → return empty array
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$json = file_get_contents($file);
if ($json === false || empty($json)) {
    echo json_encode([]);
    exit;
}

$data = json_decode($json, true);

// If JSON decoding fails → return empty array
if (!is_array($data)) {
    echo json_encode([]);
    exit;
}

echo json_encode($data);
