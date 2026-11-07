# 📔 Jurnal Kerja PKL

Aplikasi berbasis web untuk mendokumentasikan kegiatan Praktik Kerja Lapangan (PKL). Dibuat menggunakan **PHP Native** dan **MySQL**.

## ✨ Fitur Utama
1. **Landing Page (Publik):** Menampilkan profil 5 anggota kelompok dan 1 guru pembimbing.
2. **Chat Room:** Fitur komunikasi real-time antar anggota kelompok (menggunakan AJAX Polling).
3. **Task Planner:** Pencatatan tugas dari guru pembimbing dengan status (Todo, Doing, Done).
4. **Jurnal Aktivitas:** Dokumentasi kegiatan harian berupa teks dan unggahan album foto.
5. **Absensi Harian:** Pencatatan kehadiran anggota (Hadir, Izin, Sakit, Alpha) beserta rekapnya.
6. **Manajemen Profil:** Setiap anggota dapat mengubah nama, bio, dan foto profil mereka.

## 🛠️ Teknologi yang Digunakan
- **Backend:** PHP 8.x (Native)
- **Database:** MySQL (PDO)
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Keamanan:** Password Hashing (`password_hash`), PDO Prepared Statements (Anti SQL Injection), Session Management.

## 🚀 Cara Menjalankan di Localhost (XAMPP)
1. Clone repository ini ke dalam folder `C:\xampp\htdocs\`.
2. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`, buat database baru bernama `jurnal_pkl`.
4. Import file `install.sql` ke dalam database tersebut.
5. Buka file `config/db.php` dan sesuaikan `BASE_URL` dengan nama folder Anda (contoh: `http://localhost/jurnal_pkl`).
6. Akses web melalui browser: `http://localhost/jurnal_pkl/`.

## 🔐 Akun Default
- **Anggota 1:** `farrel` | Pass: `123`
- **Guru:** `guru` | Pass: `123`
*(Segera ganti password dan username melalui database setelah login pertama).*

## 👥 Anggota Kelompok
1. [Farrel] (Ketua)
2. [Zahran]
3. [Saif]
4. [Lupi]
5. [Hamzah]

**Guru Pembimbing:** [Nama Guru]
**Instansi PKL:** [Nama Dinas/Perusahaan]