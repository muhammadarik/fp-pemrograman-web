<!DOCTYPE html>
<html>
<head>
    <title>Vasta Computer</title>
    <!-- <link rel="stylesheet" href="<?= $base_url_admin ?>assets/css/style.css"> -->
    <link rel="stylesheet" href="<?= $base_url_admin ?>assets/bootstrap5/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light mb-5"style="background-color: #e3f2fd;">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $base_url_admin ?>">Vasta Computer</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="?page=dashboard">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?page=product/index">Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?page=productCategory/index">Category Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?page=users">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?logout=true">Keluar</a>
        </li>
    </div>
  </div>
</nav>