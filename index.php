<?php
// index.php
require_once __DIR__ . '/includes/functions.php';

// Ambil semua user untuk ditampilkan
$stmt = $pdo->query("SELECT name, role, bio, jurusan, sekolah, photo FROM users ORDER BY role DESC, id ASC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .hero { text-align: center; padding: 50px 20px; background: #2b6cb0; color: white; margin-bottom: 30px; border-radius: 8px; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .grid-profil { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card-profil { background: white; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card-profil img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid #e2e8f0; }
        .card-profil h3 { margin-bottom: 5px; color: #2d3748; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; margin-bottom: 10px; }
        .badge-guru { background: #fefcbf; color: #744210; }
        .badge-siswa { background: #bee3f8; color: #2a4365; }
    </style>
</head>
<body>
    <nav class="navbar" style="margin-bottom: 0; border-radius: 0;">
        <div class="nav-brand"><a href="#">📔 <?= APP_NAME ?></a></div>
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/login.php" class="btn" style="margin-left: 0;">Login Anggota</a>
        </div>
    </nav>

    <main class="container">
        <div class="hero">
            <h1>Selamat Datang di Jurnal PKL</h1>
            <p>Website dokumentasi kegiatan Praktik Kerja Lapangan (PKL) Kelompok Kami.</p>
        </div>

        <h2 style="margin-bottom: 20px; text-align: center;">Profil Kelompok</h2>
        <div class="grid-profil">
            <?php foreach ($users as $u): ?>
                <div class="card-profil">
                    <?php 
                    // Tampilkan foto profil atau avatar default huruf depan
                    $foto = !empty($u['photo']) ? UPLOAD_URL . $u['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($u['name']) . '&background=random&size=150';
                    ?>
                    <img src="<?= $foto ?>" alt="<?= e($u['name']) ?>">
                    
                    <?php if ($u['role'] === 'teacher'): ?>
                        <div class="badge badge-guru">Guru Pembimbing</div>
                    <?php else: ?>
                        <div class="badge badge-siswa">Anggota</div>
                    <?php endif; ?>
                    
                    <h3><?= e($u['name']) ?></h3>
                    <p style="font-size: 14px; color: #718096; margin-bottom: 10px;">
                        <?= e($u['jurusan'] ?? '-') ?> <br> <?= e($u['sekolah'] ?? '-') ?>
                    </p>
                    <p style="font-size: 13px;"><?= nl2br(e($u['bio'] ?? 'Belum ada bio.')) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> <?= APP_NAME ?></p>
    </footer>
</body>
</html>