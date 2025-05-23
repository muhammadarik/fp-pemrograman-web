<!DOCTYPE html>
<html>
<head>
    <title>Landing Page</title>
    <link rel="stylesheet" href="<?= $base_url_user ?>assets/cssku/style.css">
    <link rel="stylesheet" href="<?= $base_url_user ?>assets/bootstrap5/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">
  <div class="container">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="?page=home">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="?page=register">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="?page=login">Home</a>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>