<?php
// Aktifkan session (jika belum)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
$host = "localhost";
$user = "root"; // Ganti sesuai hosting jika live
$pass = "";     // Ganti sesuai hosting jika live
$db   = "vastacom_db"; // Ganti jika perlu

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// BASE URL otomatis (deteksi protokol dan host)
$base_path = "/fp-pemrograman-web/"; // <- ganti jika folder project berbeda
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $base_path;

// URL untuk admin dan user
$base_url_user = $base_url;
$base_url_admin = $base_url . "admin/";
