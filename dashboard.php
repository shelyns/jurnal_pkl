<?php
// dashboard.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

// Hitung total tugas yang belum selesai
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status != 'done'");
$total_tugas = $stmt->fetchColumn();

// Cek absen hari ini
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT status FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user['id'], $today]);
$absen_hari_ini = $stmt->fetchColumn();

?>

<h1 style="margin-bottom: 20px;">Halo, <?= e($user['name']) ?>! 👋</h1>
<p style="margin-bottom: 30px; color: #4a5568;">
    Anda login sebagai <strong><?= $user['role'] === 'teacher' ? 'Guru Pembimbing' : 'Anggota Kelompok' ?></strong>.
</p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
    <!-- Kartu Tugas -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #ecc94b;">
        <h3 style="color: #718096; font-size: 14px; text-transform: uppercase; margin-bottom: 10px;">Tugas Belum Selesai</h3>
        <p style="font-size: 32px; font-weight: bold; color: #2d3748;"><?= $total_tugas ?></p>
        <a href="tasks.php" style="font-size: 14px;">Lihat Tugas →</a>
    </div>

    <!-- Kartu Absen -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #48bb78;">
        <h3 style="color: #718096; font-size: 14px; text-transform: uppercase; margin-bottom: 10px;">Absen Hari Ini</h3>
        <p style="font-size: 24px; font-weight: bold; color: <?= $absen_hari_ini ? '#38a169' : '#e53e3e' ?>;">
            <?= $absen_hari_ini ? strtoupper($absen_hari_ini) : 'Belum Absen' ?>
        </p>
        <a href="attendance.php" style="font-size: 14px;">Isi Absen →</a>
    </div>

    <!-- Kartu Chat -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #4299e1;">
        <h3 style="color: #718096; font-size: 14px; text-transform: uppercase; margin-bottom: 10px;">Komunikasi</h3>
        <p style="font-size: 16px; font-weight: bold; color: #2d3748; margin-bottom: 10px;">Chat Room Kelompok</p>
        <a href="chat.php" class="btn">Buka Chat</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>