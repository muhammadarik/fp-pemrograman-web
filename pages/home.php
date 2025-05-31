<?php
include 'config/config.php';

$query = "SELECT * FROM produk LIMIT 9";
$result = mysqli_query($conn, $query);

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Professional Laptop Repair & Service</h1>
            <p class="lead mb-5">Fast, reliable, and affordable laptop repair services with warranty guarantee</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="booking.php" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-calendar-check me-2"></i>Book Service Now
                </a>
                <a href="#services" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-tools me-2"></i>Our Services
                </a>
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
        <h2 class="text-center section-title">Our Products</h2>
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
        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-primary px-4">View All Products</a>
        </div>
    </div>
</section>


    <!-- Why Us Section -->
    <section id="why-us" class="why-us-section py-5">
        <div class="container">
            <h2 class="text-center section-title">Why Choose Vasta Computer?</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Fast Service</h3>
                        <p>Most repairs completed within 24-48 hours with real-time status updates.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Warranty</h3>
                        <p>90-day warranty on all repairs and parts for your peace of mind.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Experts</h3>
                        <p>Certified technicians with 5+ years of experience in laptop repairs.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>Notifications</h3>
                        <p>Get SMS/email updates about your repair status at every stage.</p>
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