<?php
// profile.php
require_once __DIR__ . '/komponen/header.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$currentUserData = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    $jurusan = trim($_POST['jurusan']);
    $sekolah = trim($_POST['sekolah']);
    $new_photo = $currentUserData['photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_photo = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_DIR . $new_photo)) {
                if (!empty($currentUserData['photo']) && file_exists(UPLOAD_DIR . $currentUserData['photo'])) unlink(UPLOAD_DIR . $currentUserData['photo']);
            }
        } else {
            setFlash('error', 'Format foto harus JPG/PNG.');
        }
    }

    if (empty($_SESSION['flash'])) {
        $pdo->prepare("UPDATE users SET name = ?, bio = ?, jurusan = ?, sekolah = ?, photo = ? WHERE id = ?")->execute([$name, $bio, $jurusan, $sekolah, $new_photo, $user['id']]);
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $new_photo;
        setFlash('success', 'Profil diperbarui!');
        redirect('profile.php');
    }
}

$fotoUrl = !empty($currentUserData['photo']) ? UPLOAD_URL . $currentUserData['photo'] : 'https://ui-avatars.com/api/?name=' . urlencode($currentUserData['name']) . '&background=EEF2FF&color=4F46E5&size=200';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">👤 Pengaturan Profil</h1>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="md:flex">
        <div class="bg-gray-50 dark:bg-gray-900/50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700 md:w-1/3">
            <img src="<?= $fotoUrl ?>" class="w-40 h-40 rounded-full object-cover ring-4 ring-white dark:ring-gray-700 shadow-lg mb-4">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white text-center"><?= e($currentUserData['name']) ?></h2>
            <p class="text-sm text-primary dark:text-indigo-400 font-medium mb-1">@<?= e($currentUserData['email']) ?></p>
        </div>

        <div class="p-8 md:w-2/3">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= e($currentUserData['name']) ?>" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Profil</label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-primary dark:file:bg-indigo-900/50 dark:file:text-indigo-300 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 cursor-pointer">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio Singkat</label>
                    <textarea name="bio" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary outline-none"><?= e($currentUserData['bio']) ?></textarea>
                </div>
                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/komponen/footer.php'; ?>