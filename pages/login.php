<?php
$message = '';
// Ambil pesan dari session jika ada dan tampilkan
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Hapus pesan setelah diambil
}

// Proses form login ketika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Panggil fungsi loginUser dari auth.php (yang sudah di-include oleh index.php)
    $login_result = loginUser($conn, $username, $password); // $conn tersedia dari index.php

    // Simpan pesan hasil login ke session untuk ditampilkan
    $_SESSION['message'] = $login_result['message'];

    if ($login_result['status']) {
        // Login berhasil, redirect berdasarkan role
        $role = $login_result['role'];

        if ($role == 'admin') {
            // Redirect ke dashboard admin (luar router user)
            header("Location: " . BASE_URL . "/admin/pages/dashboard.php");
            exit;
        } elseif ($role == 'pelanggan') {
            // Redirect ke dashboard pelanggan melalui router utama
            header("Location: " . BASE_URL . "/index.php?page=dashboard");
            exit;
        } elseif ($role == 'teknisi') {
            // Redirect ke dashboard teknisi (luar router user)
            header("Location: " . BASE_URL . "/admin/pages/dashboard_teknisi.php"); // Asumsi lokasi
            exit;
        }
    } else {
        // Login gagal, redirect kembali ke halaman login melalui router utama
        header("Location: " . BASE_URL . "/index.php?page=login");
        exit;
    }
}
?>

<style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background-color: #fff;
        }
    </style>


<div class="login-container">
    <h2 class="text-center mb-4">Login ke Akun Anda</h2>

    <form action="" method="POST">
        <?php if (!empty($message) && !$login_result['status']): // Tampilkan pesan error jika ada ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </div>
    </form>

    <p class="text-center mt-3">Belum punya akun? <a href="<?= $base_url_user ?>?page=register">Daftar di sini</a></p>
</div>