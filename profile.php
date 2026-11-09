<?php
// profile.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$message = '';
$msg_type = '';

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
        $tmp_name = $_FILES['photo']['tmp_name'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_photo = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($tmp_name, UPLOAD_DIR . $new_photo)) {
                if (!empty($currentUserData['photo']) && file_exists(UPLOAD_DIR . $currentUserData['photo'])) {
                    unlink(UPLOAD_DIR . $currentUserData['photo']);
                }
            }
        } else {
            $message = 'Format foto harus JPG, PNG, atau WEBP.';
            $msg_type = 'error';
        }
    }

    if (empty($message)) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, jurusan = ?, sekolah = ?, photo = ? WHERE id = ?");
        $stmt->execute([$name, $bio, $jurusan, $sekolah, $new_photo, $user['id']]);
        
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $new_photo;

        setFlash('success', 'Profil berhasil diperbarui!');
        redirect('profile.php');
    }
}

$fotoUrl = !empty($currentUserData['photo']) 
    ? UPLOAD_URL . $currentUserData['photo'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($currentUserData['name']) . '&background=EEF2FF&color=4F46E5&size=200';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">👤 Pengaturan Profil</h1>
    <p class="text-gray-500 mt-1">Perbarui informasi publik dan foto Anda.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="md:flex">
        <!-- Sisi Kiri: Foto -->
        <div class="bg-gray-50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 md:w-1/3">
            <img src="<?= $fotoUrl ?>" alt="Foto Profil" class="w-40 h-40 rounded-full object-cover ring-4 ring-white shadow-lg mb-4">
            <h2 class="text-lg font-bold text-gray-800 text-center"><?= e($currentUserData['name']) ?></h2>
            <p class="text-sm text-primary font-medium mb-1">@<?= e($currentUserData['email']) ?></p>
            <span class="text-xs text-gray-500 bg-gray-200 px-3 py-1 rounded-full font-semibold mt-2"><?= $currentUserData['role'] === 'teacher' ? 'Guru Pembimbing' : 'Anggota PKL' ?></span>
        </div>

        <!-- Sisi Kanan: Form -->
        <div class="p-8 md:w-2/3">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= e($currentUserData['name']) ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ganti Foto Profil</label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-primary hover:file:bg-indigo-100 border border-gray-200 rounded-lg bg-gray-50 cursor-pointer">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                        <input type="text" name="jurusan" value="<?= e($currentUserData['jurusan']) ?>" placeholder="Contoh: RPL" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Asal Sekolah</label>
                        <input type="text" name="sekolah" value="<?= e($currentUserData['sekolah']) ?>" placeholder="Contoh: SMK N 1 Contoh" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bio Singkat</label>
                    <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang Anda..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none"><?= e($currentUserData['bio']) ?></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="<?= BASE_URL ?>/dashboard.php" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">Batal</a>
                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>