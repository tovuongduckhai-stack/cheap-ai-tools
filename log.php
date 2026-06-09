<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$db = new PDO('sqlite:' . __DIR__ . '/data.db');
$db->exec("CREATE TABLE IF NOT EXISTS events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event TEXT,
    payload TEXT,
    ip TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['event'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid input']);
    exit;
}

$stmt = $db->prepare("INSERT INTO events (event, payload, ip) VALUES (?, ?, ?)");
$stmt->execute([
    $input['event'],
    json_encode($input['payload'] ?? []),
    $_SERVER['REMOTE_ADDR'] ?? 'unknown'
]);

echo json_encode(['status' => 'ok']);
