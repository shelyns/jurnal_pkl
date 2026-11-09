<?php
// journal.php
require_once __DIR__ . '/komponen/header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $activity_date = $_POST['activity_date'];

    if ($title && $activity_date) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO journals (user_id, title, content, activity_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user['id'], $title, $content, $activity_date]);
            $journal_id = $pdo->lastInsertId();

            if (!empty($_FILES['photos']['name'][0])) {
                $total_files = count($_FILES['photos']['name']);
                if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);

                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['photos']['tmp_name'][$i];
                        $name = $_FILES['photos']['name'][$i];
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $new_filename = 'jurnal_' . $journal_id . '_' . time() . '_' . $i . '.' . $ext;
                            if (move_uploaded_file($tmp_name, UPLOAD_DIR . $new_filename)) {
                                $stmtPhoto = $pdo->prepare("INSERT INTO journal_photos (journal_id, filename) VALUES (?, ?)");
                                $stmtPhoto->execute([$journal_id, $new_filename]);
                            }
                        }
                    }
                }
            }
            $pdo->commit();
            setFlash('success', 'Jurnal berhasil disimpan!');
            redirect('journal.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash('error', 'Gagal: ' . $e->getMessage());
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT user_id FROM journals WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() == $user['id'] || $user['role'] === 'teacher') {
        $stmtPhotos = $pdo->prepare("SELECT filename FROM journal_photos WHERE journal_id = ?");
        $stmtPhotos->execute([$id]);
        foreach ($stmtPhotos->fetchAll() as $p) @unlink(UPLOAD_DIR . $p['filename']);
        $pdo->prepare("DELETE FROM journals WHERE id = ?")->execute([$id]);
        setFlash('success', 'Jurnal dihapus.');
    }
    redirect('journal.php');
}

$journals = $pdo->query("SELECT j.*, u.name, u.role FROM journals j JOIN users u ON j.user_id = u.id ORDER BY j.activity_date DESC, j.created_at DESC")->fetchAll();
$photosByJournal = [];
if ($journals) {
    $ids = implode(',', array_column($journals, 'id'));
    foreach ($pdo->query("SELECT journal_id, filename FROM journal_photos WHERE journal_id IN ($ids)")->fetchAll() as $p) {
        $photosByJournal[$p['journal_id']][] = $p['filename'];
    }
}
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📸 Jurnal Aktivitas</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Dokumentasikan setiap kegiatan PKL Anda.</p>
    </div>
    <button onclick="document.getElementById('form-add').classList.toggle('hidden')" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Tulis Jurnal
    </button>
</div>

<div id="form-add" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-3">Buat Entri Jurnal</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Kegiatan</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal</label>
                <input type="date" name="activity_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
            <textarea name="content" rows="4" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none"></textarea>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Foto (Bisa banyak)</label>
            <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-primary hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 cursor-pointer">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm">Simpan Jurnal</button>
        </div>
    </form>
</div>

<div class="space-y-6">
    <?php if (empty($journals)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
            <div class="text-5xl mb-4">📭</div>
            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-1">Belum ada jurnal</h3>
            <p class="text-gray-500 dark:text-gray-400">Jadilah yang pertama menulis dokumentasi!</p>
        </div>
    <?php endif; ?>

    <?php foreach ($journals as $j): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex justify-between items-start mb-4 border-b border-gray-100 dark:border-gray-700 pb-4 gap-4">
                <div>
                    <div class="inline-block bg-indigo-50 dark:bg-indigo-900/50 text-primary dark:text-indigo-300 text-xs font-bold px-3 py-1 rounded-full mb-2">
                        <?= tanggalIndo($j['activity_date']) ?>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?= e($j['title']) ?></h3>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Oleh: <span class="text-primary dark:text-indigo-400"><?= e($j['name']) ?></span></p>
                    <?php if ($j['role'] === 'teacher'): ?>
                        <span class="text-xs text-yellow-600 bg-yellow-50 dark:bg-yellow-900/30 px-2 py-0.5 rounded border border-yellow-100 dark:border-yellow-800">Guru</span>
                    <?php endif; ?>
                    <?php if ($j['user_id'] == $user['id'] || $user['role'] === 'teacher'): ?>
                        <a href="?delete=<?= $j['id'] ?>" onclick="return confirm('Hapus jurnal?')" class="block text-xs text-red-500 hover:text-red-700 mt-2 font-medium">Hapus</a>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap mb-6"><?= e($j['content']) ?></p>
            <?php if (!empty($photosByJournal[$j['id']])): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <?php foreach ($photosByJournal[$j['id']] as $foto): ?>
                        <a href="<?= UPLOAD_URL . $foto ?>" target="_blank" class="group relative aspect-square overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700 block">
                            <img src="<?= UPLOAD_URL . $foto ?>" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/komponen/footer.php'; ?>