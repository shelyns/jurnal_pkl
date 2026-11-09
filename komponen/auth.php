<?php
// komponen/auth.php
// Middleware login & role

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

/** Wajib login, kalau tidak → redirect ke login */
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        setFlash('error', 'Silakan login terlebih dahulu.');
        redirect('login.php');
    }
}

/** Wajib role tertentu */
function requireRole($role) {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== $role) {
        setFlash('error', 'Akses ditolak.');
        redirect('dashboard.php');
    }
}

/** Ambil user aktif dari session */
function currentUser() {
    return [
        'id'    => $_SESSION['user_id']   ?? null,
        'name'  => $_SESSION['name']      ?? null,
        'email' => $_SESSION['email']     ?? null,
        'role'  => $_SESSION['role']      ?? null,
        'photo' => $_SESSION['photo']     ?? null,
    ];
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}