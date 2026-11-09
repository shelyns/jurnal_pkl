<?php
// api/pesan_send.php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Sembunyikan error HTML agar JSON tidak rusak

if (empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$content = trim($input['content'] ?? '');

if ($content !== '') {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        // Jika database error, kirim pesan error ke javascript
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Pesan kosong']);
}