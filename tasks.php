<?php
// tasks.php
require_once __DIR__ . '/komponen/header.php';
requireLogin();

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

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'Tugas dihapus.');
    redirect('tasks.php');
}

$stmt = $pdo->query("SELECT * FROM tasks ORDER BY CASE status WHEN 'todo' THEN 1 WHEN 'doing' THEN 2 WHEN 'done' THEN 3 END, deadline ASC, created_at DESC");
$tasks = $stmt->fetchAll();
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📋 Daftar Tugas PKL</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola tugas dari guru pembimbing atau pekerjaan harian.</p>
    </div>
    <button onclick="document.getElementById('form-add').classList.toggle('hidden')" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Tugas
    </button>
</div>

<div id="form-add" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-3">Tugas Baru</h3>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Tugas</label>
            <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Guru Pembimbing</label>
                <input type="text" name="teacher_name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deadline</label>
                <input type="date" name="deadline" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none">
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none"></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm">Simpan Tugas</button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($tasks)): ?>
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-10">Belum ada tugas.</div>
    <?php endif; ?>
    <?php foreach ($tasks as $t): 
        $borderColor = $t['status'] === 'todo' ? 'border-t-red-500' : ($t['status'] === 'doing' ? 'border-t-yellow-400' : 'border-t-green-500');
        $bgOpacity = $t['status'] === 'done' ? 'opacity-60' : '';
    ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 border-t-4 <?= $borderColor ?> <?= $bgOpacity ?> p-5 transition-all">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2"><?= e($t['title']) ?></h3>
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 space-y-1">
                <p>👨‍🏫 <?= e($t['teacher_name'] ?: '-') ?></p>
                <p>📅 <?= $t['deadline'] ? tanggalIndo($t['deadline']) : 'Tanpa deadline' ?></p>
            </div>
            <?php if ($t['description']): ?>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2"><?= e($t['description']) ?></p>
            <?php endif; ?>
            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                <select class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-primary outline-none cursor-pointer" onchange="updateStatus(<?= $t['id'] ?>, this.value)">
                    <option value="todo" <?= $t['status'] === 'todo' ? 'selected' : '' ?>>🔴 Todo</option>
                    <option value="doing" <?= $t['status'] === 'doing' ? 'selected' : '' ?>>🟡 Doing</option>
                    <option value="done" <?= $t['status'] === 'done' ? 'selected' : '' ?>>🟢 Done</option>
                </select>
                <a href="?delete=<?= $t['id'] ?>" onclick="return confirm('Hapus tugas ini?')" class="text-red-500 dark:text-red-400 hover:text-red-700 text-sm font-medium">Hapus</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    async function updateStatus(id, status) {
        try {
            await fetch('api/task_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: status })
            });
            location.reload();
        } catch (e) { alert('Gagal update status.'); }
    }
</script>

<?php require_once __DIR__ . '/komponen/footer.php'; ?>