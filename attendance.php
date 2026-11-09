<?php
// attendance.php
require_once __DIR__ . '/komponen/header.php';
requireLogin();

$today = date('Y-m-d');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'absen') {
    $status = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');
    if (in_array($status, ['hadir', 'izin', 'sakit', 'alpha'])) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE user_id = ? AND date = ?");
            $stmt->execute([$user['id'], $today]);
            if ($stmt->fetch()) {
                setFlash('error', 'Sudah absen hari ini.');
            } else {
                $pdo->prepare("INSERT INTO attendance (user_id, date, status, note) VALUES (?, ?, ?, ?)")->execute([$user['id'], $today, $status, $note]);
                setFlash('success', 'Absen disimpan!');
            }
        } catch (Exception $e) { setFlash('error', $e->getMessage()); }
        redirect('attendance.php');
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
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📅 Absensi Harian</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Tanggal: <span class="font-semibold text-primary dark:text-indigo-400"><?= tanggalIndo($today) ?></span></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <?php if ($absenSaya): ?>
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-50 dark:bg-green-900/30 rounded-full mb-4 text-green-500 dark:text-green-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Sudah Absen!</h3>
                    <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold bg-indigo-50 dark:bg-indigo-900/50 text-primary dark:text-indigo-300 uppercase mt-2">
                        <?= $absenSaya['status'] ?>
                    </div>
                    <?php if ($absenSaya['note']): ?>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 italic">"<?= e($absenSaya['note']) ?>"</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-3">Isi Kehadiran</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="absen">
                    <div class="space-y-3 mb-5">
                        <?php foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $val => $label): ?>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input type="radio" name="status" value="<?= $val ?>" required class="w-4 h-4 text-primary bg-gray-100 border-gray-300 focus:ring-primary dark:focus:ring-primary dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-3 font-medium text-gray-700 dark:text-gray-300"><?= $label ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                        <input type="text" name="note" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white py-3 rounded-xl font-bold shadow-sm">Kirim Absen</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-bold text-gray-800 dark:text-white">Rekap Anggota Hari Ini</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800">
                            <th class="py-3 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Nama</th>
                            <th class="py-3 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php if (empty($rekapAbsen)): ?>
                            <tr><td colspan="2" class="text-center py-8 text-gray-400 dark:text-gray-500">Belum ada data absen.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($rekapAbsen as $r): 
                            $badge = match($r['status']) {
                                'hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                'izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                'sakit' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'alpha' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                            };
                        ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-50 dark:border-gray-700 last:border-0 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="font-medium text-gray-800 dark:text-white"><?= e($r['name']) ?></div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500"><?= $r['role'] === 'teacher' ? 'Guru' : 'Anggota' ?></div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $badge ?>"><?= $r['status'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/komponen/footer.php'; ?>