<!DOCTYPE html>
<html>
<head>
    <title>Vasta Computer | Service - Jual Beli Laptop</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/fontawesome/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>">Vasta Computer</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php
        // Cek jika halaman saat ini adalah 'dashboard'
        if ($page == 'dashboard'):
            // Jika di dashboard, hanya tampilkan tombol Logout
            // Pastikan user sudah login sebelum menampilkan logout
            if (isLoggedIn()):
        ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/?page=logout">Logout</a>
            </li>
            <?php if ($currentUser && $currentUser['role'] == 'pelanggan'): // Tampilkan nama user jika login sebagai pelanggan ?>
                <li class="nav-item">
                    <span class="nav-link">Halo, <?= htmlspecialchars($currentUser['username']) ?>!</span>
                </li>
            <?php endif; ?>
        <?php
            endif;
        else:
            // Jika bukan di dashboard, tampilkan menu normal (Home, Registrasi, Login/Dashboard, Logout)
        ?>
            <li class="nav-item">
              <a class="nav-link <?= ($page == 'home') ? 'active' : '' ?>" aria-current="page" href="<?= BASE_URL ?>/">Home</a>
            </li>
            <?php if (!isLoggedIn()): // Tampilkan Registrasi dan Login jika belum login ?>
            <li class="nav-item">
              <a class="nav-link <?= ($page == 'register') ? 'active' : '' ?>" aria-current="page" href="<?= BASE_URL ?>/?page=register">Registrasi</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($page == 'login') ? 'active' : '' ?>" aria-current="page" href="<?= BASE_URL ?>/?page=login">Login</a>
            </li>
            <?php else: // Tampilkan Dashboard dan Logout jika sudah login ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" aria-current="page" href="<?= BASE_URL ?>/?page=dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/?page=logout">Logout</a>
                </li>
                <?php if ($currentUser && $currentUser['role'] == 'pelanggan'): // Tampilkan nama user jika login sebagai pelanggan ?>
                    <li class="nav-item">
                        <span class="nav-link">Halo, <?= htmlspecialchars($currentUser['username']) ?>!</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php
        endif;
        ?>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>