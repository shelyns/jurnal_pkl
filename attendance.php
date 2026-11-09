<?php
// attendance.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$today = date('Y-m-d');
$message = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'absen') {
    $status = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');

    if (in_array($status, ['hadir', 'izin', 'sakit', 'alpha'])) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE user_id = ? AND date = ?");
            $stmt->execute([$user['id'], $today]);
            
            if ($stmt->fetch()) {
                $message = 'Anda sudah melakukan absen hari ini.';
                $msg_type = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO attendance (user_id, date, status, note) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], $today, $status, $note]);
                setFlash('success', 'Absen berhasil disimpan!');
                redirect('attendance.php');
            }
        } catch (Exception $e) {
            $message = 'Gagal menyimpan absen: ' . $e->getMessage();
            $msg_type = 'error';
        }
    }
}

$stmt = $pdo->prepare("SELECT status, note FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user['id'], $today]);
$absenSaya = $stmt->fetch();

$stmt = $pdo->prepare("SELECT a.status, a.note, u.name, u.role FROM attendance a JOIN users u ON a.user_id = u.id WHERE a.date = ? ORDER BY u.role DESC, u.id ASC");
$stmt->execute([$today]);
$rekapAbsen = $stmt->fetchAll();
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">📅 Absensi Harian</h1>
    <p class="text-gray-500 mt-1">Tanggal: <span class="font-semibold text-primary"><?= tanggalIndo($today) ?></span></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <?php if ($absenSaya): ?>
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-50 rounded-full mb-4 text-green-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Sudah Absen!</h3>
                    <p class="text-sm text-gray-500 mb-4">Anda tercatat hari ini.</p>
                    
                    <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold bg-indigo-50 text-primary uppercase">
                        <?= $absenSaya['status'] ?>
                    </div>
                    <?php if ($absenSaya['note']): ?>
                        <p class="mt-4 text-sm text-gray-500 italic">"<?= e($absenSaya['note']) ?>"</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-3">Isi Kehadiran</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="absen">
                    
                    <div class="space-y-3 mb-5">
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="hadir" required class="w-4 h-4 text-primary focus:ring-primary">
                            <span class="ml-3 font-medium text-gray-700">Hadir</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="izin" required class="w-4 h-4 text-primary focus:ring-primary">
                            <span class="ml-3 font-medium text-gray-700">Izin</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="sakit" required class="w-4 h-4 text-primary focus:ring-primary">
                            <span class="ml-3 font-medium text-gray-700">Sakit</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="status" value="alpha" required class="w-4 h-4 text-primary focus:ring-primary">
                            <span class="ml-3 font-medium text-gray-700">Alpha (Tanpa Kabar)</span>
                        </label>
                    </div>
                    
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <input type="text" name="note" placeholder="Contoh: Sakit demam" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    
                    <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition-colors shadow-sm">Kirim Absen</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800">Rekap Anggota Hari Ini</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-sm text-gray-500 bg-white">
                            <th class="py-3 px-6 font-semibold border-b">Nama</th>
                            <th class="py-3 px-6 font-semibold border-b">Status</th>
                            <th class="py-3 px-6 font-semibold border-b">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php if (empty($rekapAbsen)): ?>
                            <tr><td colspan="3" class="text-center py-8 text-gray-400">Belum ada data absen.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach ($rekapAbsen as $r): 
                            // Menggunakan IF-ELSE agar kompatibel dengan PHP versi berapapun
                            $badge = 'bg-gray-100 text-gray-700';
                            if ($r['status'] === 'hadir') $badge = 'bg-green-100 text-green-700';
                            elseif ($r['status'] === 'izin') $badge = 'bg-blue-100 text-blue-700';
                            elseif ($r['status'] === 'sakit') $badge = 'bg-yellow-100 text-yellow-700';
                            elseif ($r['status'] === 'alpha') $badge = 'bg-red-100 text-red-700';
                        ?>
                            <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-0">
                                <td class="py-4 px-6">
                                    <div class="font-medium text-gray-800"><?= e($r['name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= $r['role'] === 'teacher' ? 'Guru Pembimbing' : 'Anggota' ?></div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $badge ?>"><?= $r['status'] ?></span>
                                </td>
                                <td class="py-4 px-6 text-gray-600"><?= e($r['note'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>