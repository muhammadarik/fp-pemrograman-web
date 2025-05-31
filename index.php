<?php
session_start(); // Selalu mulai session di awal setiap permintaan
include "config/config.php";
include "functions/auth.php";
// --- AMBIL PARAMETER PAGE ---
// Ambil parameter 'page' dari URL, default ke 'home'
$page = $_GET['page'] ?? 'home';
$allowed_pages = [
    'home'              => 'pages/home.php',              // Landing page sebelum login
    'login'             => 'pages/login.php',             // Halaman login
    'register'          => 'pages/register.php',          // Halaman registrasi
    'dashboard'         => 'pages/dashboard.php',         // Dashboard setelah login (untuk pelanggan)
    'logout'            => 'pages/logout.php',            // Halaman logout
    // Tambahkan halaman pelanggan lainnya di sini jika ada:
    // 'booking'           => 'pages/booking.php',
    // 'service_history'   => 'pages/service_history.php',
    // 'product_catalog'   => 'pages/product_catalog.php',
    // 'hash_generator'    => 'pages/hash_generator.php', // Jika ini adalah halaman yang dapat diakses pengguna
];
// --- TENTUKAN FILE YANG AKAN DI-INCLUDE & PENANGANAN HALAMAN TIDAK DITEMUKAN ---
$include_file = $allowed_pages[$page] ?? null;

// Jika halaman tidak ada di daftar allowed_pages atau file tidak ditemukan, arahkan ke halaman home
if (!$include_file || !file_exists($include_file)) {
    $_SESSION['message'] = "Halaman tidak ditemukan."; // Set pesan error di session
    header("Location: " . BASE_URL . "/index.php?page=home"); // Redirect ke home page melalui router
    exit(); // Hentikan eksekusi skrip
}

// --- LOGIKA PENGAMANAN AKSES (AUTORISASI & REDIRECT) ---

// PENTING: Panggil getCurrentUser() DI SINI di index.php
// agar variabel $currentUser tersedia di semua halaman yang di-include
$currentUser = null; // Inisialisasi dulu
if (isLoggedIn()) {
    $currentUser = getCurrentUser($conn); // Panggil fungsi ini!
}

// 1. Jika user sudah login dan mencoba mengakses 'login' atau 'register', arahkan ke dashboard
if (($page == 'login' || $page == 'register') && isLoggedIn()) {
    header("Location: " . BASE_URL . "/index.php?page=dashboard"); // Redirect ke dashboard melalui router
    exit(); // Hentikan eksekusi skrip
}

// 2. Untuk halaman yang memerlukan login (selain 'home', 'login', 'register'), pastikan user sudah login
$public_pages = ['home', 'login', 'register']; // Halaman yang bisa diakses tanpa login
if (!in_array($page, $public_pages) && !isLoggedIn()) {
    $_SESSION['message'] = "Anda harus login untuk mengakses halaman ini."; // Set pesan error
    header("Location: " . BASE_URL . "/index.php?page=login"); // Redirect ke halaman login melalui router
    exit(); // Hentikan eksekusi skrip
}

// 3. Penanganan role-based access untuk dashboard pelanggan
if ($page == 'dashboard') {
    $currentUser = getCurrentUser($conn); // Dapatkan data user yang login
    // Periksa apakah user adalah 'pelanggan'. checkAccess akan mengurus redirect jika tidak.
    checkAccess($currentUser, ['pelanggan'], 'index.php?page=login'); // Pelanggan saja yang bisa akses dashboard ini

    // Jika user adalah admin atau teknisi (meskipun mereka melewati isLoggedIn() di atas),
    // kita harus mengarahkan mereka ke dashboard khusus mereka.
    if ($currentUser && $currentUser['role'] == 'admin') {
        header("Location: " . BASE_URL . "/admin/pages/dashboard.php"); // Redirect ke dashboard admin
        exit();
    } elseif ($currentUser && $currentUser['role'] == 'teknisi') {
         header("Location: " . BASE_URL . "/admin/pages/dashboard_teknisi.php"); // Redirect ke dashboard teknisi
         exit();
    }
}



// $page = isset($_GET['page']) ? $_GET['page'] : 'home';
// $file = "pages/" . $page . ".php";
// if (file_exists($file)) {
//     include $file;
// } else {
//     echo "<h1>404 - Halaman tidak ditemukan</h1>";
// }
include "inc/header.php";
include $include_file;
include "inc/footer.php";
?>
