<?php
// tasks.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

// Proses Tambah Tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $teacher = trim($_POST['teacher_name']);
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

    if ($title) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, teacher_name, deadline, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $teacher, $deadline, $user['id']]);
        setFlash('success', 'Tugas berhasil ditambahkan!');
        redirect('tasks.php');
    }
}

// Proses Hapus Tugas
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'Tugas dihapus.');
    redirect('tasks.php');
}

// Ambil semua tugas
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY 
    CASE status WHEN 'todo' THEN 1 WHEN 'doing' THEN 2 WHEN 'done' THEN 3 END, 
    deadline ASC, created_at DESC");
$tasks = $stmt->fetchAll();
?>

<style>
    .task-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
    .task-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid #ccc; }
    .task-card.todo { border-color: #e53e3e; }
    .task-card.doing { border-color: #ecc94b; }
    .task-card.done { border-color: #48bb78; opacity: 0.7; }
    .task-title { font-weight: bold; font-size: 16px; margin-bottom: 5px; }
    .task-meta { font-size: 12px; color: #718096; margin-bottom: 10px; }
    .task-desc { font-size: 14px; margin-bottom: 15px; }
    .task-actions { display: flex; justify-content: space-between; align-items: center; }
    .status-select { padding: 4px; border-radius: 4px; font-size: 12px; border: 1px solid #cbd5e0; background: #f7fafc; cursor: pointer; }
    
    .form-add { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; display: none; }
    .form-add.active { display: block; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>📋 Daftar Tugas PKL</h2>
    <button onclick="document.getElementById('form-add').classList.toggle('active')">+ Tambah Tugas</button>
</div>

<!-- Form Tambah Tugas -->
<div id="form-add" class="form-add">
    <h3 style="margin-bottom: 15px;">Tambah Tugas Baru</h3>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <label>Nama Tugas / Pekerjaan</label>
        <input type="text" name="title" required placeholder="Contoh: Input data barang">
        
        <div class="form-row">
            <div>
                <label>Diberikan Oleh (Guru)</label>
                <input type="text" name="teacher_name" placeholder="Nama Guru / Pembimbing">
            </div>
            <div>
                <label>Tenggat Waktu (Deadline)</label>
                <input type="date" name="deadline">
            </div>
        </div>
        
        <label>Deskripsi / Keterangan (Opsional)</label>
        <textarea name="description" rows="3" placeholder="Detail tugas..."></textarea>
        
        <button type="submit">Simpan Tugas</button>
    </form>
</div>

<!-- List Tugas -->
<div class="task-grid">
    <?php if (empty($tasks)): ?>
        <p style="color: #718096;">Belum ada tugas yang ditambahkan.</p>
    <?php endif; ?>

    <?php foreach ($tasks as $t): ?>
        <div class="task-card <?= $t['status'] ?>" id="card-<?= $t['id'] ?>">
            <div class="task-title"><?= e($t['title']) ?></div>
            <div class="task-meta">
                👨‍🏫 <?= e($t['teacher_name'] ?: 'Tidak disebutkan') ?> <br>
                📅 <?= $t['deadline'] ? tanggalIndo($t['deadline']) : 'Tanpa deadline' ?>
            </div>
            
            <?php if ($t['description']): ?>
                <div class="task-desc"><?= nl2br(e($t['description'])) ?></div>
            <?php endif; ?>

            <div class="task-actions">
                <!-- Select status untuk update cepat via AJAX -->
                <select class="status-select" onchange="updateStatus(<?= $t['id'] ?>, this.value)">
                    <option value="todo" <?= $t['status'] === 'todo' ? 'selected' : '' ?>>🔴 Todo (Belum)</option>
                    <option value="doing" <?= $t['status'] === 'doing' ? 'selected' : '' ?>>🟡 Doing (Proses)</option>
                    <option value="done" <?= $t['status'] === 'done' ? 'selected' : '' ?>>🟢 Done (Selesai)</option>
                </select>

                <a href="?delete=<?= $t['id'] ?>" onclick="return confirm('Yakin hapus tugas ini?')" style="color: #e53e3e; font-size: 13px;">Hapus</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Fungsi untuk update status tanpa refresh halaman
    async function updateStatus(id, status) {
        // Ubah warna border card secara instan (animasi visual)
        const card = document.getElementById('card-' + id);
        card.className = 'task-card ' + status;
        
        if (status === 'done') card.style.opacity = '0.7';
        else card.style.opacity = '1';

        // Kirim perubahan ke server via AJAX
        try {
            await fetch('api/task_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: status })
            });
        } catch (e) {
            alert('Gagal update status. Cek koneksi internet.');
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>