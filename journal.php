<?php
// journal.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

// Proses Tambah Jurnal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $activity_date = $_POST['activity_date'];

    if ($title && $activity_date) {
        try {
            $pdo->beginTransaction();

            // 1. Simpan data jurnal
            $stmt = $pdo->prepare("INSERT INTO journals (user_id, title, content, activity_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user['id'], $title, $content, $activity_date]);
            $journal_id = $pdo->lastInsertId();

            // 2. Proses Upload Foto (Album)
            if (!empty($_FILES['photos']['name'][0])) {
                $total_files = count($_FILES['photos']['name']);
                
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0777, true);
                }

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
            setFlash('success', 'Jurnal dan foto berhasil disimpan!');
            redirect('journal.php');

        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }
}

// Proses Hapus Jurnal
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT user_id FROM journals WHERE id = ?");
    $stmt->execute([$id]);
    $owner = $stmt->fetchColumn();

    if ($owner == $user['id'] || $user['role'] === 'teacher') {
        $stmtPhotos = $pdo->prepare("SELECT filename FROM journal_photos WHERE journal_id = ?");
        $stmtPhotos->execute([$id]);
        $photos = $stmtPhotos->fetchAll();
        foreach ($photos as $p) {
            @unlink(UPLOAD_DIR . $p['filename']);
        }

        $stmt = $pdo->prepare("DELETE FROM journals WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Jurnal berhasil dihapus.');
    } else {
        setFlash('error', 'Anda tidak berhak menghapus jurnal ini.');
    }
    redirect('journal.php');
}

// Ambil semua jurnal beserta fotonya
$journals = $pdo->query("
    SELECT j.*, u.name, u.role 
    FROM journals j 
    JOIN users u ON j.user_id = u.id 
    ORDER BY j.activity_date DESC, j.created_at DESC
")->fetchAll();

// Kelompokkan foto berdasarkan journal_id
$photosByJournal = [];
if ($journals) {
    $ids = implode(',', array_column($journals, 'id'));
    $allPhotos = $pdo->query("SELECT journal_id, filename FROM journal_photos WHERE journal_id IN ($ids)")->fetchAll();
    foreach ($allPhotos as $p) {
        $photosByJournal[$p['journal_id']][] = $p['filename'];
    }
}
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">📸 Jurnal Aktivitas</h1>
        <p class="text-gray-500 mt-1">Dokumentasikan setiap kegiatan PKL Anda di sini.</p>
    </div>
    <button onclick="document.getElementById('form-add').classList.toggle('hidden')" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tulis Jurnal Baru
    </button>
</div>

<!-- Form Tambah Jurnal (Tersembunyi secara default) -->
<div id="form-add" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-3">Buat Entri Jurnal</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kegiatan</label>
                <input type="text" name="title" required placeholder="Contoh: Pengecekan Server Dinas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kegiatan</label>
                <input type="date" name="activity_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kegiatan</label>
            <textarea name="content" rows="4" placeholder="Ceritakan detail pekerjaan atau kegiatan hari ini..." required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"></textarea>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto (Bisa pilih banyak sekaligus)</label>
            <input type="file" name="photos[]" multiple accept="image/jpeg, image/png, image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-primary hover:file:bg-indigo-100 transition-all cursor-pointer border border-gray-300 rounded-lg bg-gray-50">
            <p class="text-xs text-gray-500 mt-2">Format didukung: JPG, PNG, WEBP.</p>
        </div>
        
        <div class="flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('form-add').classList.add('hidden')" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">Batal</button>
            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm">Simpan Jurnal</button>
        </div>
    </form>
</div>

<!-- List Jurnal -->
<div class="space-y-6">
    <?php if (empty($journals)): ?>
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <div class="text-5xl mb-4">📭</div>
            <h3 class="text-lg font-medium text-gray-800 mb-1">Belum ada jurnal</h3>
            <p class="text-gray-500">Jadilah yang pertama menulis dokumentasi kegiatan PKL!</p>
        </div>
    <?php endif; ?>

    <?php foreach ($journals as $j): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4 border-b border-gray-100 pb-4 gap-4">
                <div>
                    <div class="inline-block bg-indigo-50 text-primary text-xs font-bold px-3 py-1 rounded-full mb-2">
                        <?= tanggalIndo($j['activity_date']) ?>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900"><?= e($j['title']) ?></h3>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm text-gray-600 font-medium">Oleh: <span class="text-primary"><?= e($j['name']) ?></span></p>
                    <?php if ($j['role'] === 'teacher'): ?>
                        <span class="text-xs text-yellow-600 font-semibold bg-yellow-50 px-2 py-0.5 rounded border border-yellow-100">Guru</span>
                    <?php endif; ?>
                    
                    <?php if ($j['user_id'] == $user['id'] || $user['role'] === 'teacher'): ?>
                        <a href="?delete=<?= $j['id'] ?>" onclick="return confirm('Hapus jurnal ini beserta fotonya?')" class="block text-xs text-red-500 hover:text-red-700 mt-2 font-medium">Hapus</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap mb-6"><?= e($j['content']) ?></p>
            
            <!-- Galeri Foto Modern -->
            <?php if (!empty($photosByJournal[$j['id']])): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <?php foreach ($photosByJournal[$j['id']] as $foto): ?>
                        <a href="<?= UPLOAD_URL . $foto ?>" target="_blank" class="group relative aspect-square overflow-hidden rounded-xl bg-gray-100 block">
                            <img src="<?= UPLOAD_URL . $foto ?>" alt="Foto Kegiatan" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>