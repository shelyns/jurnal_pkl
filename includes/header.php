<?php
// includes/header.php
// Dipakai semua halaman setelah login
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
$user = currentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-brand">
        <a href="<?= BASE_URL ?>/dashboard.php">📔 <?= APP_NAME ?></a>
    </div>
    <div class="nav-links">
        <a href="<?= BASE_URL ?>/dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>/chat.php">Chat</a>
        <a href="<?= BASE_URL ?>/tasks.php">Tugas</a>
        <a href="<?= BASE_URL ?>/journal.php">Jurnal</a>
        <a href="<?= BASE_URL ?>/attendance.php">Absen</a>
        <a href="<?= BASE_URL ?>/profile.php">Profil</a>
        <a href="<?= BASE_URL ?>/logout.php" class="btn-logout">Logout</a>
    </div>
</nav>
<main class="container">
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>