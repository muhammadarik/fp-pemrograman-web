<?php
include "../config/config.php";

if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-danger align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>'
        . $_SESSION['message'] .
        '</div>';
    unset($_SESSION['message']); // hapus agar tidak muncul terus
}

if (isset($_POST['login'])) {
    // Escape input untuk keamanan (meskipun prepared statement lebih baik)
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password']; // jangan htmlspecialchars di password

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username= '$user'");

    if (!$query) {
        echo "Query error: " . mysqli_error($conn);
        exit;
    }

    $data = mysqli_fetch_assoc($query);
    if (!$data) {
        echo "Username tidak ditemukan.";
    } else {
        // Debug: cek hash password dari db
        // var_dump($data['password']); exit;

        if (password_verify($pass, $data['password'])) {
            $_SESSION['admin_id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];
            header("Location: ../admin/index.php");
            exit;
        } else {
            echo "Password salah.";
        }
    }
}
?>
<!-- <link rel="stylesheet" href="<?= $base_url_admin ?>assets/css/style.css"> -->
<link rel="stylesheet" href="<?= $base_url_admin ?>assets/bootstrap5/css/bootstrap.min.css">
<script src="<?= $base_url_admin ?>assets/bootstrap5/js/bootstrap.bundle.min.js"></script>


<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <!-- <span class="d-none d-lg-block">NiceAdmin</span> -->
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login Admin</h5>
                  </div>

                  <form class="row g-3 needs-validation" novalidate="" method="post">
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <input type="text" name="username" class="form-control" id="yourUsername" required="">
                      <div class="invalid-feedback">Please enter your username!</div>
                    </div>
                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required="">
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" name="login" type="submit">Login</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
</section>
