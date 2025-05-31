<?php
include 'config/config.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $no_hp    = $_POST['no_hp'];

    // Insert ke users
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'pelanggan')");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $user_id = $stmt->insert_id;

    // Insert ke pelanggan
    $stmt2 = $conn->prepare("INSERT INTO pelanggan (id, `users.id`, nama, email, no_hp) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("iisss", $user_id, $user_id, $nama, $email, $no_hp);
    $stmt2->execute();

    echo "Pendaftaran berhasil. <a href='login.php'>Login</a>";
}
?>

<form method="POST">
    <input name="username" placeholder="Username" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <input name="nama" placeholder="Nama Lengkap" required><br>
    <input name="email" placeholder="Email" required><br>
    <input name="no_hp" placeholder="No HP" required><br>
    <button name="register">Daftar</button>
</form>
