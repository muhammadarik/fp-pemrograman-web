<?php
include '../config/config.php';
$query = "SELECT * FROM produk p where p.status = '1'";
$result = mysqli_query($conn, $query);

//mengambil data SERVICE untuk total section
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

error_reporting (E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

?>

<section id="dashboard" class="dashboard">
    <div class="container">
        <h1>Dashboard</h1>
        <p>Selamat datang di halaman dashboard <strong><?= $_SESSION['username'] ?></strong></p>
    </div>
</section>
    
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-12 p-4">

                <div class="tab-content">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane fade show active" id="dashboard">
                        <!-- Stat Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-tools card-icon"></i>
                                        <h5 class="card-title">Total Service</h5>
                                        <h2 class="card-text"><?= $total_service ?>+</h2>
                                        <!-- <a href="#services" class="text-white stretched-link" data-bs-toggle="tab"></a> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people card-icon"></i>
                                        <h5 class="card-title">Pelanggan</h5>
                                        <h2 class="card-text"><?= $total_produk ?>+</h2>
                                        <a href="#customers" class="text-white stretched-link" data-bs-toggle="tab"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card bg-warning text-dark">
                                    <div class="card-body text-center">
                                        <i class="bi bi-receipt card-icon"></i>
                                        <h5 class="card-title">Invoice Pending</h5>
                                        <h2 class="card-text">8</h2>
                                        <a href="#invoices" class="text-dark stretched-link" data-bs-toggle="tab"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card bg-info text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-cash-coin card-icon"></i>
                                        <h5 class="card-title">Pendapatan Bulan Ini</h5>
                                        <h2 class="card-text">Rp12.450.000</h2>
                                        <a href="#reports" class="text-white stretched-link" data-bs-toggle="tab"></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Services and Status Chart -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Service Terbaru</h5>
                                        <a href="#services" class="btn btn-sm btn-outline-primary" data-bs-toggle="tab">Lihat Semua</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Pelanggan</th>
                                                        <th>Jenis Kerusakan</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>#SVC-125</td>
                                                        <td>Budi Santoso</td>
                                                        <td>Laptop Tidak Nyala</td>
                                                        <td><span class="badge bg-warning text-dark">Diproses</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#SVC-124</td>
                                                        <td>Ani Wijaya</td>
                                                        <td>Ganti Layar HP</td>
                                                        <td><span class="badge bg-success">Selesai</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#SVC-123</td>
                                                        <td>Rudi Hartono</td>
                                                        <td>Instalasi Software</td>
                                                        <td><span class="badge bg-secondary">Menunggu</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#SVC-122</td>
                                                        <td>Siti Rahayu</td>
                                                        <td>Printer Error</td>
                                                        <td><span class="badge bg-danger">Dibatalkan</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Statistik Service</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="serviceStatusChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Invoices -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Invoice Terbaru</h5>
                                        <a href="#invoices" class="btn btn-sm btn-outline-primary" data-bs-toggle="tab">Lihat Semua</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No. Invoice</th>
                                                        <th>Service ID</th>
                                                        <th>Pelanggan</th>
                                                        <th>Total Biaya</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>#INV-2023-125</td>
                                                        <td>#SVC-120</td>
                                                        <td>Dewi Lestari</td>
                                                        <td>Rp1.250.000</td>
                                                        <td><span class="badge bg-success">Lunas</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#INV-2023-124</td>
                                                        <td>#SVC-118</td>
                                                        <td>Fajar Setiawan</td>
                                                        <td>Rp750.000</td>
                                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>#INV-2023-123</td>
                                                        <td>#SVC-115</td>
                                                        <td>Hendra Kurniawan</td>
                                                        <td>Rp2.100.000</td>
                                                        <td><span class="badge bg-danger">Batal</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other tabs would go here -->
                    <div class="tab-pane fade" id="services">...</div>
                    <div class="tab-pane fade" id="customers">...</div>
                    <div class="tab-pane fade" id="technicians">...</div>
                    <div class="tab-pane fade" id="invoices">...</div>
                    <div class="tab-pane fade" id="products">...</div>
                    <div class="tab-pane fade" id="categories">...</div>
                    <div class="tab-pane fade" id="reports">...</div>
                    <div class="tab-pane fade" id="settings">...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Service Status Chart
        const ctx = document.getElementById('serviceStatusChart').getContext('2d');
        const serviceStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Diproses', 'Selesai', 'Dibatalkan'],
                datasets: [{
                    data: [15, 8, 95, 6],
                    backgroundColor: [
                        '#6c757d',
                        '#ffc107',
                        '#198754',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>


<style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>