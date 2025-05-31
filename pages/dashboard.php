<?php
// pages/dashboard.php
// TIDAK PERLU include '../config/config.php';
// include '../functions/auth.php';

// Semua variabel dari index.php (termasuk $conn, BASE_URL) sudah tersedia di sini
// Variabel $currentUser sudah di-set oleh checkAccess di index.php jika user berhasil melewati otorisasi
$currentUser = getCurrentUser($conn); // Panggil lagi untuk mendapatkan data lengkap di sini

if (!$currentUser || $currentUser['role'] !== 'pelanggan') {
    // Seharusnya sudah di-redirect oleh index.php, tapi ini sebagai fallback atau untuk keamanan ekstra
    $_SESSION['message'] = "Akses ditolak atau Anda bukan pelanggan.";
    header("Location: " . $base_url_user . "/index.php?page=login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Vanta Computer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { max-width: 960px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .welcome-message { text-align: center; margin-bottom: 30px; color: #555; font-size: 1.1em; }
        .menu-options { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 30px; }
        .menu-item {
            border: 1px solid #eee;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            flex: 1 1 calc(33% - 40px); /* 3 item per baris pada layar lebar */
            min-width: 250px; /* Lebar minimum item */
            background-color: #fff;
            transition: transform 0.2s;
        }
        .menu-item:hover { transform: translateY(-5px); }
        .menu-item a { text-decoration: none; color: #333; font-weight: bold; display: block; }
        .menu-item a:hover { color: #007bff; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .logout-link { text-align: center; margin-top: 40px; }
        .logout-link a { color: #dc3545; text-decoration: none; font-weight: bold; }
        .logout-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <p class="welcome-message">Selamat datang di Dashboard, **<?php echo htmlspecialchars($currentUser['username']); ?>**!</p>
        <h2>Apa yang ingin Anda lakukan hari ini?</h2>

        <div class="menu-options">
            <div class="menu-item">
                <a href="booking.php">
                    <h3>Booking Service Baru</h3>
                    <p>Pesan layanan perbaikan atau instalasi komputer Anda.</p>
                </a>
            </div>
            <div class="menu-item">
                <a href="service_history.php">
                    <h3>Riwayat & Status Service</h3>
                    <p>Lihat perkembangan perbaikan dan semua layanan yang pernah Anda gunakan.</p>
                </a>
            </div>
            <div class="menu-item">
                <a href="product_catalog.php">
                    <h3>Katalog Produk</h3>
                    <p>Telusuri berbagai produk komputer dan aksesoris yang kami jual.</p>
                </a>
            </div>
            </div>

        <div class="logout-link">
            <p><a href="<?= BASE_URL ?>/?page=logout">Logout</a></p>
        </div>
    </div>