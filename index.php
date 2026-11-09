<?php
// index.php
require_once __DIR__ . '/includes/functions.php';

$stmt = $pdo->query("SELECT name, role, bio, jurusan, sekolah, photo FROM users ORDER BY role DESC, id ASC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { primary: '#4F46E5' } } } }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 h-16 flex justify-between items-center">
            <a href="#" class="text-xl font-bold text-primary flex items-center gap-2 hover:no-underline">
                <span class="text-2xl">📔</span> Jurnal PKL
            </a>
            <a href="<?= BASE_URL ?>/login.php" class="bg-primary text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                Login Anggota
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="max-w-6xl mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
            Dokumentasi Kegiatan <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-500">Praktik Kerja Lapangan</span>
        </h1>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-8">
            Platform terpusat untuk mencatat jurnal harian, absensi, tugas, dan komunikasi kelompok PKL kami.
        </p>
    </header>

    <!-- Profil Grid -->
    <main class="max-w-6xl mx-auto px-4 pb-24">
        <h2 class="text-2xl font-bold text-center mb-10 text-gray-800">Tim Kelompok Kami</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($users as $u): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 text-center group">
                    <?php 
                    $foto = !empty($u['photo']) ? UPLOAD_URL . $u['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($u['name']) . '&background=EEF2FF&color=4F46E5&size=150';
                    ?>
                    <div class="relative inline-block mb-4">
                        <img src="<?= $foto ?>" alt="<?= e($u['name']) ?>" class="w-24 h-24 rounded-full object-cover mx-auto ring-4 ring-gray-50 group-hover:ring-indigo-50 transition-all">
                        <?php if ($u['role'] === 'teacher'): ?>
                            <span class="absolute bottom-0 right-0 bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full border-2 border-white">Guru</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-800 mb-1"><?= e($u['name']) ?></h3>
                    <p class="text-sm text-primary font-medium mb-3"><?= e($u['jurusan'] ?: 'Anggota PKL') ?></p>
                    
                    <p class="text-sm text-gray-500 line-clamp-3">
                        <?= e($u['bio'] ?: 'Belum ada deskripsi bio.') ?>
                    </p>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
                        <?= e($u['sekolah'] ?: 'SMK N 1 Contoh') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
        </div>
    </footer>
</body>
</html>