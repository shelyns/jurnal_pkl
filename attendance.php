<?php
// attendance.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$today = date('Y-m-d');
$message = '';
$msg_type = '';

// Proses simpan absen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'absen') {
    $status = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');

    if (in_array($status, ['hadir', 'izin', 'sakit', 'alpha'])) {
        try {
            // Cek apakah hari ini sudah absen
            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE user_id = ? AND date = ?");
            $stmt->execute([$user['id'], $today]);
            
            if ($stmt->fetch()) {
                $message = 'Anda sudah melakukan absen hari ini.';
                $msg_type = 'error';
            } else {
                // Simpan absen baru
                $stmt = $pdo->prepare("INSERT INTO attendance (user_id, date, status, note) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], $today, $status, $note]);
                $message = 'Absen berhasil disimpan!';
                $msg_type = 'success';
            }
        } catch (Exception $e) {
            $message = 'Gagal menyimpan absen: ' . $e->getMessage();
            $msg_type = 'error';
        }
    }
}

// Cek absen user hari ini
$stmt = $pdo->prepare("SELECT status, note FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user['id'], $today]);
$absenSaya = $stmt->fetch();

// Ambil semua data absen hari ini (untuk rekap)
$stmt = $pdo->prepare("
    SELECT a.status, a.note, u.name, u.role 
    FROM attendance a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.date = ? 
    ORDER BY u.role DESC, u.id ASC
");
$stmt->execute([$today]);
$rekapAbsen = $stmt->fetchAll();
?>

<style>
    .absen-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .rekap-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .rekap-table th, .rekap-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #edf2f7; font-size: 14px; }
    .rekap-table th { background: #f7fafc; font-weight: 600; color: #4a5568; }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: capitalize; }
    .badge-hadir { background: #c6f6d5; color: #22543d; }
    .badge-izin { background: #bee3f8; color: #2a4365; }
    .badge-sakit { background: #fefcbf; color: #744210; }
    .badge-alpha { background: #fed7d7; color: #822727; }
    .status-done { text-align: center; padding: 30px; }
</style>

<h2 style="margin-bottom: 20px;">📅 Absen Harian (<?= tanggalIndo($today) ?>)</h2>

<?php if ($message): ?>
    <div class="flash flash-<?= $msg_type ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="absen-card">
    <?php if ($absenSaya): ?>
        <div class="status-done">
            <h3 style="color: #38a169; margin-bottom: 10px;">✅ Anda Sudah Absen Hari Ini</h3>
            <p style="margin-bottom: 5px;">Status: <span class="badge badge-<?= $absenSaya['status'] ?>"><?= $absenSaya['status'] ?></span></p>
            <?php if ($absenSaya['note']): ?>
                <p style="color: #718096; font-size: 14px;">Catatan: <?= e($absenSaya['note']) ?></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h3 style="margin-bottom: 15px;">Form Absen Hari Ini</h3>
        <form method="POST">
            <input type="hidden" name="action" value="absen">
            
            <label>Status Kehadiran</label>
            <select name="status" required>
                <option value="" disabled selected>-- Pilih Status --</option>
                <option value="hadir">Hadir</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpha">Alpha (Tanpa Keterangan)</option>
            </select>
            
            <label>Catatan (Opsional)</label>
            <input type="text" name="note" placeholder="Contoh: Izin ke dokter gigi">
            
            <button type="submit">Kirim Absen</button>
        </form>
    <?php endif; ?>
</div>

<!-- Tabel Rekap Absen Hari Ini -->
<h3 style="margin-bottom: 15px;">Rekap Absen Hari Ini</h3>
<table class="rekap-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Peran</th>
            <th>Status</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rekapAbsen)): ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #718096;">Belum ada yang absen hari ini.</td>
            </tr>
        <?php endif; ?>
        
        <?php foreach ($rekapAbsen as $i => $r): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= e($r['name']) ?></strong></td>
                <td><?= $r['role'] === 'teacher' ? 'Guru Pembimbing' : 'Anggota' ?></td>
                <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
                <td style="color: #718096;"><?= e($r['note'] ?: '-') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>