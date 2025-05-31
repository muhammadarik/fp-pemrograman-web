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

// // URL untuk admin dan user
// $base_url_user = $base_url;
$base_url_admin = $base_url . "admin/";

// Definisikan BASE_URL untuk konsistensi URL di seluruh aplikasi
// Sesuaikan ini dengan URL root proyek Anda di XAMPP/server
define('BASE_URL', 'http://localhost/fp-pemrograman-web'); // Contoh URL lokal

// Fungsi untuk menampilkan pesan flash (di config.php agar bisa diakses oleh semua halaman)
// Pesan ini akan muncul sebagai alert JavaScript sebelum halaman dimuat.
if (isset($_SESSION['message'])) {
    $js_message = addslashes($_SESSION['message']); // Escape untuk JavaScript
    echo "<script>alert('{$js_message}');</script>";
    unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
}
?>
