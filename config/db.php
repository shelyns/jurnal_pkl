<?php
// config/db.php
// Konfigurasi koneksi database MySQL

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP default kosong. Ganti jika hosting.
define('DB_NAME', 'jurnal_pkl');

define('APP_NAME', 'Jurnal Kerja PKL');
define('BASE_URL', 'http://localhost/jurnal_pkl'); // sesuaikan folder Anda
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

// Koneksi PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . htmlspecialchars($e->getMessage()));
}

// Timezone Indonesia
date_default_timezone_set('Asia/Jakarta');