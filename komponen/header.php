<?php
// komponen/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
$user = currentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    
    <!-- Tailwind CSS & Dark Mode Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // Mengaktifkan fitur dark mode dengan class
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#4F46E5', secondary: '#10B981' }
                }
            }
        }
    </script>
    
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Script Pencegah Flicker (Kedipan Putih saat pindah halaman di dark mode) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen transition-colors duration-300 dark:bg-gray-900 dark:text-gray-100">
    
    <!-- Navbar Modern -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-100 dark:bg-gray-800/80 dark:border-gray-700 transition-colors duration-300">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <a href="<?= BASE_URL ?>/dashboard.php" class="text-xl font-bold text-primary flex items-center gap-2 hover:no-underline dark:text-indigo-400">
                        <span class="text-2xl">📔</span> Jurnal PKL
                    </a>
                </div>
                
                <!-- Menu Navigasi Tengah -->
                <div class="hidden md:flex items-center space-x-1">
                    <?php 
                    $current = basename($_SERVER['PHP_SELF']);
                    $menus = [
                        'dashboard.php' => 'Dashboard',
                        'diskusi.php' => 'Diskusi',
                        'tasks.php' => 'Tugas',
                        'journal.php' => 'Jurnal',
                        'attendance.php' => 'Absen',
                    ];
                    foreach ($menus as $file => $label): 
                        $isActive = ($current === $file);
                        $activeClass = $isActive 
                            ? 'bg-indigo-50 text-primary dark:bg-indigo-900/50 dark:text-indigo-300' 
                            : 'text-gray-600 hover:bg-gray-100 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white';
                    ?>
                        <a href="<?= BASE_URL ?>/<?= $file ?>" class="px-3 py-2 rounded-md text-sm font-medium transition-colors <?= $activeClass ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Kanan: Dark Mode Toggle & Profil -->
                <div class="flex items-center gap-4">
                    
                    <!-- Tombol Dark Mode -->
                    <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none rounded-lg text-sm p-2.5 transition-colors">
                        <!-- Ikon Matahari (Muncul di Dark Mode) -->
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <!-- Ikon Bulan (Muncul di Light Mode) -->
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>

                    <!-- Dropdown Profil -->
                    <div class="relative">
                        <button id="dropdownProfileButton" data-dropdown-toggle="dropdownProfile" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-white focus:outline-none transition-colors">
                            <?php 
                            $navFoto = !empty($user['photo']) ? UPLOAD_URL . $user['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=EEF2FF&color=4F46E5';
                            ?>
                            <img src="<?= $navFoto ?>" alt="Foto Profil" class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600">
                            <span class="hidden md:block"><?= explode(' ', $user['name'])[0] ?></span>
                            <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <!-- Isi Dropdown -->
                        <div id="dropdownProfile" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 z-50">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?= e($user['name']) ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= $user['role'] === 'teacher' ? 'Guru Pembimbing' : 'Anggota' ?></p>
                            </div>
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="<?= BASE_URL ?>/profile.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= BASE_URL ?>/logout.php" class="block px-4 py-2 hover:bg-red-50 text-red-600 dark:hover:bg-red-900/20 dark:text-red-400 flex items-center gap-2 rounded-b-xl">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Container Utama -->
    <main class="max-w-6xl mx-auto px-4 py-8 flex-grow w-full animate-fade-in">
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3 shadow-sm <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800' : 'bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800' ?>">
                <span><?= $flash['type'] === 'success' ? '✅' : '⚠️' ?></span>
                <span class="text-sm font-medium"><?= e($flash['msg']) ?></span>
            </div>
        <?php endif; ?>
        
        <style>
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        </style>