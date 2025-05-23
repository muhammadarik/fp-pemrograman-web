<form method="post">
    <h2>Login</h2>
    <input type="text" name="username"><br>
    <input type="password" name="password"><br>
    <button type="submit" name="login">Login</button>
</form>
<?php
if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($pass, $data['password'])) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin' || $data['role'] == 'teknisi') {
            header("Location: admin/");
        } else {
            header("Location: index.php");
        }
    } else {
        echo "Login gagal!";
    }
}
?>