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
                
                // Pastikan folder upload ada
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0777, true);
                }

                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['photos']['tmp_name'][$i];
                        $name = $_FILES['photos']['name'][$i];
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        
                        // Validasi ekstensi gambar
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            // Buat nama file unik
                            $new_filename = 'jurnal_' . $journal_id . '_' . time() . '_' . $i . '.' . $ext;
                            
                            if (move_uploaded_file($tmp_name, UPLOAD_DIR . $new_filename)) {
                                // Simpan nama file ke tabel journal_photos
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
    
    // Cek apakah jurnal ini milik user yang login (atau guru)
    $stmt = $pdo->prepare("SELECT user_id FROM journals WHERE id = ?");
    $stmt->execute([$id]);
    $owner = $stmt->fetchColumn();

    if ($owner == $user['id'] || $user['role'] === 'teacher') {
        // Hapus file foto dari folder
        $stmtPhotos = $pdo->prepare("SELECT filename FROM journal_photos WHERE journal_id = ?");
        $stmtPhotos->execute([$id]);
        $photos = $stmtPhotos->fetchAll();
        foreach ($photos as $p) {
            @unlink(UPLOAD_DIR . $p['filename']);
        }

        // Hapus data dari database (foto ikut terhapus karena ON DELETE CASCADE)
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

<style>
    .jurnal-card { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; padding: 20px; }
    .jurnal-header { display: flex; justify-content: space-between; border-bottom: 1px solid #edf2f7; padding-bottom: 10px; margin-bottom: 15px; }
    .jurnal-date { font-weight: bold; color: #2b6cb0; }
    .jurnal-author { font-size: 13px; color: #718096; }
    .album-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 15px; }
    .album-item img { width: 100%; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer; transition: transform 0.2s; }
    .album-item img:hover { transform: scale(1.05); }
    
    .form-add { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>📸 Jurnal Aktivitas Kerja</h2>
</div>

<!-- Form Tambah Jurnal -->
<div class="form-add">
    <h3 style="margin-bottom: 15px;">Tulis Jurnal Baru</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        
        <div class="form-row">
            <div>
                <label>Judul Kegiatan</label>
                <input type="text" name="title" required placeholder="Contoh: Pengecekan Server">
            </div>
            <div>
                <label>Tanggal Kegiatan</label>
                <input type="date" name="activity_date" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        
        <label>Deskripsi Kegiatan</label>
        <textarea name="content" rows="4" placeholder="Ceritakan detail kegiatan hari ini..." required></textarea>
        
        <label>Upload Foto (Bisa pilih banyak sekaligus)</label>
        <input type="file" name="photos[]" multiple accept="image/jpeg, image/png, image/webp">
        <small style="display:block; margin-top:-10px; margin-bottom:15px; color:#718096;">Format: JPG, PNG, WEBP.</small>
        
        <button type="submit">Simpan Jurnal</button>
    </form>
</div>

<!-- List Jurnal -->
<?php if (empty($journals)): ?>
    <p style="color: #718096;">Belum ada jurnal yang ditulis.</p>
<?php endif; ?>

<?php foreach ($journals as $j): ?>
    <div class="jurnal-card">
        <div class="jurnal-header">
            <div>
                <div class="jurnal-date"><?= tanggalIndo($j['activity_date']) ?></div>
                <h3 style="margin: 5px 0;"><?= e($j['title']) ?></h3>
            </div>
            <div style="text-align: right;">
                <div class="jurnal-author">Oleh: <strong><?= e($j['name']) ?></strong> <?= $j['role'] === 'teacher' ? '(Guru)' : '' ?></div>
                <?php if ($j['user_id'] == $user['id'] || $user['role'] === 'teacher'): ?>
                    <a href="?delete=<?= $j['id'] ?>" onclick="return confirm('Hapus jurnal ini?')" style="font-size: 12px; color: #e53e3e;">Hapus Jurnal</a>
                <?php endif; ?>
            </div>
        </div>
        
        <p style="white-space: pre-wrap; font-size: 14px; color: #4a5568;"><?= e($j['content']) ?></p>
        
        <?php if (!empty($photosByJournal[$j['id']])): ?>
            <div class="album-grid">
                <?php foreach ($photosByJournal[$j['id']] as $foto): ?>
                    <a href="<?= UPLOAD_URL . $foto ?>" target="_blank" class="album-item">
                        <img src="<?= UPLOAD_URL . $foto ?>" alt="Foto Kegiatan">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>