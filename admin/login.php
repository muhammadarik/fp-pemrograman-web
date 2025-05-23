<?php
include "../config/config.php";
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($pass, $data['password'])) {
        $_SESSION['admin_id'] = $data['id'];
        $_SESSION['role'] = $data['role'];
        header("Location: index.php");
    } else {
        echo "Login gagal!";
    }
}
?>
<form method="post">
    <h2>Login Admin</h2>
    <input type="text" name="username"><br>
    <input type="password" name="password"><br>
    <button name="login">Login</button>
</form>