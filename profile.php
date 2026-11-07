<?php
// profile.php
require_once __DIR__ . '/includes/header.php';
requireLogin();

$message = '';
$msg_type = '';

// Ambil data user terbaru dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$currentUserData = $stmt->fetch();

// Proses simpan perubahan profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    $jurusan = trim($_POST['jurusan']);
    $sekolah = trim($_POST['sekolah']);
    
    $new_photo = $currentUserData['photo']; // Default pakai foto lama

    // Cek apakah ada upload foto baru
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['photo']['tmp_name'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            // Buat nama file unik
            $new_photo = 'user_' . $user['id'] . '_' . time() . '.' . $ext;
            
            if (move_uploaded_file($tmp_name, UPLOAD_DIR . $new_photo)) {
                // Hapus foto lama jika ada
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
        // Update database (kolom email tidak diubah karena kita jadikan username)
        $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, jurusan = ?, sekolah = ?, photo = ? WHERE id = ?");
        $stmt->execute([$name, $bio, $jurusan, $sekolah, $new_photo, $user['id']]);
        
        // Update session agar nama di navbar langsung berubah
        $_SESSION['name'] = $name;
        $_SESSION['photo'] = $new_photo;

        setFlash('success', 'Profil berhasil diperbarui!');
        redirect('profile.php');
    }
}

// Tampilkan foto saat ini
$fotoUrl = !empty($currentUserData['photo']) 
    ? UPLOAD_URL . $currentUserData['photo'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($currentUserData['name']) . '&background=random&size=150';
?>

<style>
    .profile-container { display: flex; gap: 30px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .profile-photo { text-align: center; flex: 0 0 200px; }
    .profile-photo img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #e2e8f0; margin-bottom: 15px; }
    .profile-form { flex: 1; }
    
    .readonly-input { background-color: #edf2f7; color: #718096; cursor: not-allowed; }
    
    @media (max-width: 768px) {
        .profile-container { flex-direction: column; }
    }
</style>

<h2 style="margin-bottom: 20px;">👤 Edit Profil</h2>

<div class="profile-container">
    <div class="profile-photo">
        <img src="<?= $fotoUrl ?>" alt="Foto Profil">
        <p style="font-size: 12px; color: #718096;">Foto saat ini</p>
    </div>
    
    <div class="profile-form">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            
            <!-- Menampilkan Username (Readonly) -->
            <label>Username Login</label>
            <input type="text" value="<?= e($currentUserData['email']) ?>" class="readonly-input" readonly title="Username tidak dapat diubah">
            
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="<?= e($currentUserData['name']) ?>" required>
            
            <label>Ganti Foto Profil (Opsional)</label>
            <input type="file" name="photo" accept="image/jpeg, image/png, image/webp">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" value="<?= e($currentUserData['jurusan']) ?>" placeholder="Contoh: RPL">
                </div>
                <div>
                    <label>Sekolah</label>
                    <input type="text" name="sekolah" value="<?= e($currentUserData['sekolah']) ?>" placeholder="Contoh: SMK N 1 Contoh">
                </div>
            </div>
            
            <label>Bio Singkat</label>
            <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang Anda..."><?= e($currentUserData['bio']) ?></textarea>
            
            <button type="submit" style="margin-top: 10px;">Simpan Perubahan</button>
            <a href="<?= BASE_URL ?>/dashboard.php" class="btn" style="background: #a0aec0; text-decoration: none; display: inline-block; padding: 8px 16px; border-radius: 4px; color: white; margin-left: 10px;">Batal</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>