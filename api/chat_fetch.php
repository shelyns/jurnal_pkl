<?php
// api/chat_fetch.php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

// Ambil 50 pesan terakhir (diurutkan dari yang terlama ke terbaru)
$stmt = $pdo->query("
    SELECT m.id, m.content, m.created_at, u.name, u.photo, u.role 
    FROM messages m 
    JOIN users u ON m.user_id = u.id 
    ORDER BY m.id DESC LIMIT 50
");
$messages = array_reverse($stmt->fetchAll());

$data = [];
foreach ($messages as $m) {
    $data[] = [
        'id'         => $m['id'],
        'content'    => $m['content'],
        'created_at' => date('H:i', strtotime($m['created_at'])),
        'name'       => $m['name'],
        'role'       => $m['role'],
        // Avatar default jika tidak punya foto
        'photo'      => !empty($m['photo']) ? UPLOAD_URL . $m['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($m['name']) . '&background=random&size=50',
        'is_me'      => ($m['name'] === $_SESSION['name']) // Cek apakah ini pesan kita sendiri
    ];
}

echo json_encode($data);