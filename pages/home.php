<?php
// include 'config/config.php';

$query = "SELECT * FROM produk p where p.status = '1' LIMIT 9";
$result = mysqli_query($conn, $query);

//mengambil data SERVICE untuk stats section
$query_service = "SELECT COUNT(*) as total_service FROM service";
$result_service = mysqli_query($conn, $query_service);
$data_service = mysqli_fetch_assoc($result_service);
$total_service = $data_service['total_service'];

//mengambil data PRODUK untuk stats section
$query_produk = "SELECT COUNT(*) as total_produk FROM produk WHERE status = '1'";
$result_produk = mysqli_query($conn, $query_produk);
$data_produk = mysqli_fetch_assoc($result_produk);
$total_produk = $data_produk['total_produk'];

//mengambil data PELANGGAN untuk stats section
$query_cust = "SELECT COUNT(*) as total_cust FROM pelanggan";
$result_cust = mysqli_query($conn, $query_cust);
$data_cust = mysqli_fetch_assoc($result_cust);
$total_cust = $data_cust['total_cust'];

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<style>
:root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --dark-color: #212529;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .hero-section {
            /* background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white; */
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .product-img {
            height: 200px;
            object-fit: contain;
            padding: 1rem;
        }
        
        .why-us-section {
            background-color: #f8f9fa;
        }
        
        .contact-section {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
        }
        
        .social-icon {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .social-icon:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .stats-section .display-4 {
            font-weight: 700;
        }
        
        .stats-section .lead {
            opacity: 0.8;
        }
        
        .btn-primary {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
</style>

<!-- Hero Section -->
        <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Professional Komputer & Laptop Repair</h1>
                    <p class="lead mb-4">Vasta Komputer adalah penyedia layanan servis komputer yang berkomitmen untuk memberikan solusi teknologi terbaik bagi individu dan bisnis. Dengan pengalaman bertahun-tahun, kami telah dipercaya untuk menangani berbagai permasalahan perangkat keras dan perangkat lunak, dari perbaikan rutin hingga upgrade sistem yang kompleks.</p>
                    <div class="d-flex gap-3">
                        <a href="#services" class="btn btn-light btn-lg px-4">Our Services</a>
                        <a href="#contact" class="btn btn-outline-dark btn-lg px-4">Contact Us</a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80" 
                         alt="Laptop Repair" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Layanan Kami</h2>
            <p class="text-center mb-4">Kami menawarkan berbagai layanan untuk memastikan komputer / laptop Anda berfungsi optimal</p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3>Perbaikan Hardware</h3>
                        <p>Mengganti atau memperbaiki komponen seperti motherboard, RAM, hard disk, dan lainnya.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-bug"></i>
                        </div>
                        <h3>Perbaikan Software & Pemeliharaan</h3>
                        <p>Instalasi ulang sistem operasi, penghapusan virus, Pembersihan sistem dan pembaruan perangkat lunak secara berkala.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3>Pemulihan Data & Peningkatan Performa</h3>
                        <p>Pemulihan data from rusak pada penyimpanan. Upgrade perangkat keras dan lunak untuk meningkatkan kecepatan dan efisiensi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

     <!-- Products Section -->
      <?php $upload_dir_url = '/assets/image/';?>
      
<section id="products" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title">Produk Kami</h2>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <?php
            $gambar = htmlspecialchars($row['gambar']);
            $gambar_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir_url . $gambar; // path server untuk file_exists
            ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if (!empty($row['gambar']) && file_exists($gambar_path)) : ?>
                        <img src="<?= htmlspecialchars($upload_dir_url . $row['gambar']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($row['nama']) ?>">
                    <?php else : ?>
                        <img src="<?= $upload_dir_url ?>default.jpg" class="card-img-top product-img" alt="Default">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['nama']) ?></h5>
                        <p class="card-text">Rp<?= number_format($row['harga'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
        <!-- <div class="text-center mt-5">
            <a href="products.php" class="btn btn-primary px-4">View All Products</a>
        </div> -->
    </div>
</section>


    <!-- Why Us Section -->
    <section id="why-us" class="why-us-section py-5">
        <div class="container">
            <h2 class="text-center section-title">Kenapa harus Vasta Komputer?</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Fast Service</h3>
                        <p>Sebagian besar perbaikan selesai dalam waktu 24-48 jam dengan pembaruan status waktu nyata</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Garansi</h3>
                        <p>Garansi 90 hari untuk semua perbaikan dan suku cadang demi ketenangan pikiran Anda.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Experts</h3>
                        <p>Teknisi berpengalaman 10+ tahun dalam perbaikan laptop.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Notifications</h3>
                        <p>Dapatkan notifikasi setiap perubahan status dengan mengakses dashboard pelanggan yang telah terdaftar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold"><?= $total_service ?>+</h2>
                    <p class="lead">Laptops Diperbaiki</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold"><?= $total_cust ?>+</h2>
                    <p class="lead">Customer Kami</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold"><?= $total_produk ?>+</h2>
                    <p class="lead">Products Tersedia</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold">24/7</h2>
                    <p class="lead">Customer Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
     <footer id="contact" class="contact-section text-white py-4 pb-5">
    <div class="container">
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6 mb-4">
                <h3>Kontak Kami</h3>
                <h5 class="my-3">
                    <i class="fa-brands fa-whatsapp"></i>
                    <a href="https://wa.me/6281554091512" class="text-white text-decoration-none" target="_blank">
                        0815-5409-1512
                    </a>
                </h5>
                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    Jl. Granit Nila No.20A, Paras, Mulung, Kec. Driyorejo, Kabupaten Gresik, Jawa Timur 61177
                </p>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <h5>Lokasi Kami</h5>
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7563.950085951548!2d112.634711!3d-7.3357081!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fd9d579a9af3%3A0xe4b78879228481a5!2sVasta%20Computer%20Driyorejo!5e1!3m2!1sid!2sid!4v1750265551918!5m2!1sid!2sid" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</footer>