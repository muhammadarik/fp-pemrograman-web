<? $_SESSION['username'] = $data['username']; ?>

<section id="dashboard" class="dashboard">
    <div class="container">
        <h1>Dashboard</h1>
        <p>Selamat datang di halaman dashboard <strong><?= $_SESSION['username'] ?></strong></p>
    </div>
</section>