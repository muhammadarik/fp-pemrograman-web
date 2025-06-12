<?php
$message = '';
// Ambil pesan dari session jika ada
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Hapus pesan setelah diambil
}

// Proses form registrasi ketika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Password akan dihash di fungsi
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);

    $registration_result = registerUserAsPelanggan($conn, $username, $password, $nama, $email, $no_hp);
    
    $message = $registration_result['message'];

    if ($registration_result['status']) {
        // Jika registrasi berhasil, redirect ke halaman login menggunakan BASE_URL
        echo "<script>alert('{$message}'); window.location.href='{$base_url_user}/?pages=login.php';</script>";
        exit;
    } else {
        // Jika gagal, tampilkan pesan error di halaman ini
        echo "<script>alert('{$message}');</script>";
    }
}

?>


<style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 500px; /* Lebar maksimum, tetapi akan menyesuaikan jika layar lebih kecil */
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background-color: #fff;
        }
    </style>

<div class="register-container">
    <h2 class="text-center mb-4">Registrasi Akun Baru</h2>
    <p class="text-center text-muted mb-4">Daftar untuk menjadi pelanggan kami dan mulai booking layanan!</p>

    <form action="" method="POST">
        <?php if (!empty($message) && !$registration_result['status']): // Tampilkan pesan error jika ada ?>
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
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" name="register" class="btn btn-primary">Daftar Akun</button>
        </div>
    </form>

    <p class="text-center mt-3">Sudah punya akun? <a href="<?= $base_url_user ?>pages/login.php">Login di sini</a></p>
</div>