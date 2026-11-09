<?php
// dashboard.php
require_once __DIR__ . '/komponen/header.php';
requireLogin();

// Ambil data statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status != 'done'");
$total_tugas = $stmt->fetchColumn();

$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user['id'], $today]);
$absen_hari_ini = $stmt->fetchColumn();
?>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden transition-colors">
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-50 dark:bg-indigo-900/20 rounded-full blur-2xl opacity-70"></div>
    
    <div class="z-10">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Halo, <?= e($user['name']) ?>! 👋</h1>
        <p class="text-gray-500 dark:text-gray-400">Selamat datang kembali di aplikasi Jurnal PKL. Jangan lupa isi jurnal dan absen hari ini ya!</p>
    </div>
    <div class="z-10 bg-indigo-50 dark:bg-indigo-900/50 text-primary dark:text-indigo-300 px-4 py-2 rounded-full text-sm font-semibold">
        <?= date('l, d F Y') ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card Tugas -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-lg text-yellow-600 dark:text-yellow-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <span class="text-3xl font-bold text-gray-800 dark:text-white"><?= $total_tugas ?></span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Tugas Belum Selesai</h3>
        <a href="tasks.php" class="inline-block mt-4 text-primary dark:text-indigo-400 text-sm font-semibold group-hover:underline">Lihat Tugas &rarr;</a>
    </div>

    <!-- Card Absen -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-green-50 dark:bg-green-900/30 p-3 rounded-lg text-green-600 dark:text-green-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-lg font-bold <?= $absen_hari_ini ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' ?>">
                <?= $absen_hari_ini ? strtoupper($absen_hari_ini) : 'Belum' ?>
            </span>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">Status Absen Hari Ini</h3>
        <a href="attendance.php" class="inline-block mt-4 text-primary dark:text-indigo-400 text-sm font-semibold group-hover:underline">Isi Absen &rarr;</a>
    </div>

    <!-- Card Chat -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
        </div>
        <h3 class="text-gray-800 dark:text-white font-bold mb-1">Chat Room</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Diskusi dengan anggota lain.</p>
        <a href="diskusi.php" class="inline-block bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Buka Chat</a>
    </div>
</div>

<?php require_once __DIR__ . '/komponen/footer.php'; ?>