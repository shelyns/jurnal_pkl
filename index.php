<?php
// index.php
require_once __DIR__ . '/komponen/functions.php';

$stmt = $pdo->query("SELECT name, role, bio, jurusan, sekolah, photo FROM users ORDER BY role DESC, id ASC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { 
            darkMode: 'class',
            theme: { extend: { 
                fontFamily: { sans: ['Inter', 'sans-serif'] }, 
                colors: { primary: '#4F46E5', primaryDark: '#3730A3' },
                animation: {
                    'float': 'float 6s ease-in-out infinite',
                    'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                },
                keyframes: {
                    float: {
                        '0%, 100%': { transform: 'translateY(0)' },
                        '50%': { transform: 'translateY(-15px)' },
                    },
                    fadeInUp: {
                        '0%': { opacity: '0', transform: 'translateY(20px)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' },
                    }
                }
            } } 
        }
    </script>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-100 transition-colors duration-300 min-h-screen flex flex-col overflow-x-hidden">

    <!-- Navbar -->
    <nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm fixed w-full top-0 z-50 border-b border-gray-100 dark:border-gray-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex justify-between items-center">
            <a href="#" class="text-2xl font-extrabold text-primary dark:text-indigo-400 flex items-center gap-2 hover:no-underline tracking-tight">
                <div class="bg-primary/10 p-2 rounded-xl">
                    <svg class="w-6 h-6 text-primary dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                Jurnal PKL
            </a>
            <div class="flex items-center gap-4">
                <button id="theme-toggle-public" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl p-2.5 transition-colors">
                    <svg id="theme-toggle-light-icon-public" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    <svg id="theme-toggle-dark-icon-public" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>
                <a href="<?= BASE_URL ?>/login.php" class="bg-primary hover:bg-primaryDark text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-primary/30 hover:-translate-y-0.5">
                    Login 
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Blob -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl -z-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-300 dark:bg-indigo-900 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-40 animate-float"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-blue-300 dark:bg-blue-900 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-40 animate-float" style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <span class="inline-block py-1 px-3 rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-primary dark:text-indigo-300 text-sm font-semibold mb-6 border border-indigo-100 dark:border-indigo-800">
                🚀 Platform Dokumentasi PKL Terpadu
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-8 leading-tight">
                Dokumentasikan <br class="hidden md:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-blue-500 to-indigo-400">
                    Masa PKL Anda
                </span>
            </h1>
            <p class="mt-4 text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-10 leading-relaxed">
                Satu tempat untuk mencatat jurnal harian, absensi, tugas, dan komunikasi tim. Dibuat khusus untuk kelompok Praktik Kerja Lapangan.
            </p>
            <div class="flex justify-center gap-4">
                <a href="<?= BASE_URL ?>/login.php" class="bg-primary hover:bg-primaryDark text-white px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-xl shadow-primary/30 hover:-translate-y-1 flex items-center gap-2">
                    Mulai Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Profil Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32 flex-grow w-full">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Kenali Tim Kami</h2>
            <div class="w-24 h-1.5 bg-primary mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($users as $index => $u): ?>
                <!-- Animasi delay menggunakan inline style -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:shadow-indigo-500/10 dark:hover:shadow-indigo-500/5 transition-all duration-300 text-center group animate-fade-in-up relative overflow-hidden" style="animation-delay: <?= $index * 0.15 ?>s; opacity: 0;">
                    
                    <!-- Garis Atas Kartu -->
                    <div class="absolute top-0 left-0 w-full h-1.5 <?= $u['role'] === 'teacher' ? 'bg-gradient-to-r from-yellow-400 to-orange-500' : 'bg-gradient-to-r from-primary to-blue-500' ?> opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative inline-block mb-6">
                        <?php 
                        $foto = !empty($u['photo']) ? UPLOAD_URL . $u['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($u['name']) . '&background=EEF2FF&color=4F46E5&size=150';
                        ?>
                        <img src="<?= $foto ?>" alt="<?= e($u['name']) ?>" class="w-28 h-28 rounded-full object-cover mx-auto ring-4 ring-gray-50 dark:ring-gray-700 group-hover:ring-indigo-100 dark:group-hover:ring-indigo-900 transition-all duration-300 relative z-10">
                        
                        <?php if ($u['role'] === 'teacher'): ?>
                            <span class="absolute -bottom-2 -right-2 bg-yellow-400 text-yellow-900 text-xs font-extrabold px-3 py-1 rounded-full border-4 border-white dark:border-gray-800 z-20 shadow-sm">GURU</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary dark:group-hover:text-indigo-400 transition-colors"><?= e($u['name']) ?></h3>
                    <p class="text-sm font-semibold text-primary dark:text-indigo-400 mb-4 uppercase tracking-wider"><?= e($u['jurusan'] ?: 'Anggota PKL') ?></p>
                    
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-6 h-16">
                        <?= e($u['bio'] ?: 'Halo! Saya adalah bagian dari tim PKL ini.') ?>
                    </p>
                    
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <?= e($u['sekolah'] ?: 'SMK N 1 Contoh') ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 pt-16 pb-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">📔</span>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Jurnal PKL</span>
                </div>
                <div class="flex gap-6 text-sm text-gray-500 dark:text-gray-400">
                    <a href="#" class="hover:text-primary dark:hover:text-indigo-400 transition-colors">Tentang</a>
                    <a href="#" class="hover:text-primary dark:hover:text-indigo-400 transition-colors">Panduan</a>
                    <a href="<?= BASE_URL ?>/login.php" class="hover:text-primary dark:hover:text-indigo-400 transition-colors">Login</a>
                </div>
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Logika Dark Mode
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon-public');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon-public');
        const themeToggleBtn = document.getElementById('theme-toggle-public');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>