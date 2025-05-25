<?php
include "../config/config.php";

// Cek apakah user sudah login
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teknisi')) {
    $_SESSION['message'] = "Silakan login terlebih dahulu.";
    header("Location: login.php"); // Redirect ke halaman login
    exit;
}

// logout
if (isset($_GET['logout'])) {
    session_unset();       // Hapus semua variabel sesi
    session_destroy();     // Hancurkan sesi
    header("Location: login.php"); // Redirect ke halaman login
    exit;
}

include "inc/header.php";
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$file = "pages/" . $page . ".php";
if (file_exists($file)) {
    include $file;
} else {
    echo "<h1>404 - Admin Page Not Found</h1>";
}
include "inc/footer.php";
?>