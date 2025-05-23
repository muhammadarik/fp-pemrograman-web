<form action="" method="post">
    <h2>Register User</h2>
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit" name="register">Daftar</button>
</form>
<?php
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')");
    echo "Pendaftaran berhasil!";
}
?>