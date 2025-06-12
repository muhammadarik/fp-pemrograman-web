<?php
// include 'config/config.php';

$query = "SELECT * FROM produk p where p.status = '1' LIMIT 9";
$result = mysqli_query($conn, $query);

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
            padding: 5rem 0;
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
        
        .nav-link {
            font-weight: 500;
        }
        
        .navbar-brand {
            font-weight: 700;
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
                    <h1 class="display-4 fw-bold mb-4">Professional Laptop Repair & IT Solutions</h1>
                    <p class="lead mb-4">Fast, reliable, and affordable repair services with 90-day warranty on all repairs.</p>
                    <div class="d-flex gap-3">
                        <a href="#services" class="btn btn-light btn-lg px-4">Our Services</a>
                        <a href="#contact" class="btn btn-outline-light btn-lg px-4">Contact Us</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80" 
                         alt="Laptop Repair" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Our Services</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3>Hardware Repair</h3>
                        <p>Motherboard, screen, keyboard, battery replacement and other hardware issues.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-bug"></i>
                        </div>
                        <h3>Software Troubleshooting</h3>
                        <p>Virus removal, OS installation, driver issues, and software optimization.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3>Data Recovery</h3>
                        <p>Recover your important files from damaged or corrupted storage devices.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

     <!-- Products Section -->
<section id="products" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title">Produk Kami</h2>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <?php
            $gambar = htmlspecialchars($row['gambar']);
            $gambar_path = "../assets/image/" . $gambar;
            ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if (!empty($row['gambar']) && file_exists($gambar_path)) : ?>
                        <img src="<?= $gambar_path ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($row['nama']) ?>">
                    <?php else : ?>
                        <img src="../assets/image/default.jpg" class="card-img-top product-img" alt="Default">
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
                        <p>Dapatkan Email di setiap perubahan status.</p>
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
                    <h2 class="display-4 fw-bold">1500+</h2>
                    <p class="lead">Laptops Repaired</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold">98%</h2>
                    <p class="lead">Success Rate</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold">50+</h2>
                    <p class="lead">Products Available</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-bold">24/7</h2>
                    <p class="lead">Customer Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
        <div class="container">
            <h2 class="text-center section-title text-white">Contact Us</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Get in Touch</h3>
                            <form>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Your Name">
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Your Email">
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Subject">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="4" placeholder="Your Message"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Our Info</h3>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    123 Tech Street, Jakarta, Indonesia
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    +62 123 4567 890
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    info@vastacomputer.com
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    Mon-Sat: 9:00 AM - 6:00 PM
                                </li>
                            </ul>
                            <div class="mt-4">
                                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                            </div>
                            <div class="mt-4">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613507864!3d-6.194741395493371!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x6b45e839560ef85!2sMonumen%20Nasional!5e0!3m2!1sen!2sid!4v1629997982545!5m2!1sen!2sid" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>