<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
$user = currentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#4F46E5', secondary: '#10B981' }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <a href="<?= BASE_URL ?>/dashboard.php" class="text-xl font-bold text-primary flex items-center gap-2 hover:no-underline">
                        <span class="text-2xl">📔</span> Jurnal PKL
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-1">
                    <?php 
                    $current = basename($_SERVER['PHP_SELF']);
                    $menus = [
                        'dashboard.php' => 'Dashboard',
                        'chat.php' => 'Chat',
                        'tasks.php' => 'Tugas',
                        'journal.php' => 'Jurnal',
                        'attendance.php' => 'Absen',
                        'profile.php' => 'Profil'
                    ];
                    foreach ($menus as $file => $label): 
                        $isActive = ($current === $file);
                        $activeClass = $isActive ? 'bg-indigo-50 text-primary' : 'text-gray-600 hover:bg-gray-50 hover:text-primary';
                    ?>
                        <a href="<?= BASE_URL ?>/<?= $file ?>" class="px-3 py-2 rounded-md text-sm font-medium transition-colors <?= $activeClass ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold leading-none text-gray-800"><?= e($user['name']) ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= $user['role'] === 'teacher' ? 'Guru Pembimbing' : 'Anggota' ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Container Utama -->
    <main class="max-w-6xl mx-auto px-4 py-8 flex-grow w-full">
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg flex items-center gap-3 <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
                <span><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span>
                <span class="text-sm font-medium"><?= e($flash['msg']) ?></span>
            </div>
        <?php endif; ?>