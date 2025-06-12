<?php
// session_start();
// require_once 'config.php'; // File koneksi database

// Cek apakah user sudah login dan memiliki role pelanggan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header('Location: login.php');
    exit;
}

// Fungsi untuk mendapatkan detail service
function getServiceDetail($conn, $service_id, $user_id) {
    // Ambil detail service
    $query_service = "SELECT s.*, u.username as nama_teknisi, p.nama as nama_pelanggan
                     FROM service s
                     LEFT JOIN users u ON s.id_teknisi = u.id
                     JOIN pelanggan p ON s.id_pelanggan = p.id
                     JOIN users us ON p.user_id = us.id
                     WHERE s.id = ? AND us.id = ?";
    $stmt_service = $conn->prepare($query_service);
    $stmt_service->bind_param("ii", $service_id, $user_id);
    $stmt_service->execute();
    $service = $stmt_service->get_result()->fetch_assoc();

    if (!$service) {
        return false;
    }

    // Ambil riwayat status
    $query_history = "SELECT hs.*, u.username as updated_by_name
                     FROM history_status hs
                     LEFT JOIN users u ON hs.updated_by = u.id
                     WHERE hs.service_id = ?
                     ORDER BY hs.updated_on DESC";
    $stmt_history = $conn->prepare($query_history);
    $stmt_history->bind_param("i", $service_id);
    $stmt_history->execute();
    $history = $stmt_history->get_result()->fetch_all(MYSQLI_ASSOC);

    // Ambil data invoice jika ada
    $query_invoice = "SELECT * FROM invoice WHERE service_id = ?";
    $stmt_invoice = $conn->prepare($query_invoice);
    $stmt_invoice->bind_param("i", $service_id);
    $stmt_invoice->execute();
    $invoice = $stmt_invoice->get_result()->fetch_assoc();

    return [
        'service' => $service,
        'history' => $history,
        'invoice' => $invoice
    ];
}

// Ambil data pelanggan
$user_id = $_SESSION['user_id'];
$query_pelanggan = "SELECT p.* FROM pelanggan p JOIN users u ON p.user_id = u.id WHERE u.id = ?";
$stmt_pelanggan = $conn->prepare($query_pelanggan);
$stmt_pelanggan->bind_param("i", $user_id);
$stmt_pelanggan->execute();
$pelanggan = $stmt_pelanggan->get_result()->fetch_assoc();

// Ambil data service pelanggan
$query_service = "SELECT s.*, u.username as nama_teknisi 
                 FROM service s 
                 LEFT JOIN users u ON s.id_teknisi = u.id 
                 WHERE s.id_pelanggan = ? 
                 ORDER BY s.tanggal_masuk DESC";
$stmt_service = $conn->prepare($query_service);
$stmt_service->bind_param("i", $pelanggan['id']);
$stmt_service->execute();
$services = $stmt_service->get_result()->fetch_all(MYSQLI_ASSOC);

// Hitung statistik
$total_service = count($services);
$menunggu = 0;
$diproses = 0;
$selesai = 0;
$dibatalkan = 0;

foreach ($services as $service) {
    switch ($service['status']) {
        case 'menunggu': $menunggu++; break;
        case 'diproses': $diproses++; break;
        case 'selesai': $selesai++; break;
        case 'dibatalkan': $dibatalkan++; break;
    }
}

// Handle AJAX request untuk detail service
if (isset($_GET['action']) && $_GET['action'] == 'get_service_detail' && isset($_GET['id'])) {
    $service_id = $_GET['id'];
    $detail = getServiceDetail($conn, $service_id, $user_id);
    
    if (!$detail) {
        die('<div class="alert alert-danger">Service tidak ditemukan</div>');
    }
    
    // Output HTML untuk detail service
    ob_start();
    ?>
    <div class="row">
        <div class="col-md-6">
            <h5>Informasi Service</h5>
            <table class="table table-sm">
                <tr>
                    <th width="40%">ID Service</th>
                    <td>#<?php echo $detail['service']['id']; ?></td>
                </tr>
                <tr>
                    <th>Pelanggan</th>
                    <td><?php echo htmlspecialchars($detail['service']['nama_pelanggan']); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Masuk</th>
                    <td><?php echo date('d M Y H:i', strtotime($detail['service']['tanggal_masuk'])); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Update</th>
                    <td><?php echo date('d M Y H:i', strtotime($detail['service']['tanggal_update'])); ?></td>
                </tr>
                <tr>
                    <th>Teknisi</th>
                    <td><?php echo $detail['service']['nama_teknisi'] ? htmlspecialchars($detail['service']['nama_teknisi']) : '-'; ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php 
                        $badge_class = '';
                        switch ($detail['service']['status']) {
                            case 'menunggu': $badge_class = 'badge-menunggu'; break;
                            case 'diproses': $badge_class = 'badge-diproses'; break;
                            case 'selesai': $badge_class = 'badge-selesai'; break;
                            case 'dibatalkan': $badge_class = 'badge-dibatalkan'; break;
                        }
                        ?>
                        <span class="status-badge <?php echo $badge_class; ?>">
                            <?php echo ucfirst($detail['service']['status']); ?>
                        </span>
                    </td>
                </tr>
            </table>

            <h5 class="mt-4">Detail Kerusakan</h5>
            <p><strong>Jenis Kerusakan:</strong><br>
            <?php echo htmlspecialchars($detail['service']['jenis_kerusakan']); ?></p>
            
            <p><strong>Deskripsi:</strong><br>
            <?php echo nl2br(htmlspecialchars($detail['service']['deskripsi'])); ?></p>
            
            <?php if ($detail['service']['foto_kerusakan']): ?>
            <p><strong>Foto Kerusakan:</strong><br>
            <img src="uploads/<?php echo htmlspecialchars($detail['service']['foto_kerusakan']); ?>" 
                 class="img-fluid rounded mt-2" style="max-height: 200px;"></p>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h5>Riwayat Status</h5>
            <div class="timeline">
                <?php if (count($detail['history']) > 0): ?>
                    <?php foreach ($detail['history'] as $item): ?>
                    <div class="timeline-item mb-3">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo htmlspecialchars($item['status']); ?></strong>
                            <small class="text-muted"><?php echo date('d M Y H:i', strtotime($item['updated_on'])); ?></small>
                        </div>
                        <div>
                            <small>Diupdate oleh: <?php echo $item['updated_by_name'] ? htmlspecialchars($item['updated_by_name']) : 'System'; ?></small>
                        </div>
                        <?php if (!empty($item['catatan'])): ?>
                        <div class="mt-1 p-2 bg-light rounded">
                            <small><?php echo nl2br(htmlspecialchars($item['catatan'])); ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada riwayat status</div>
                <?php endif; ?>
            </div>

            <?php if ($detail['invoice']): ?>
            <h5 class="mt-4">Informasi Invoice</h5>
            <table class="table table-sm">
                <tr>
                    <th width="40%">Tanggal Invoice</th>
                    <td><?php echo date('d M Y H:i', strtotime($detail['invoice']['tanggal_invoice'])); ?></td>
                </tr>
                <tr>
                    <th>Total Biaya</th>
                    <td>Rp <?php echo number_format($detail['invoice']['total_biaya'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <th>Status Pembayaran</th>
                    <td>
                        <?php 
                        $badge_class = $detail['invoice']['status_pembayaran'] == 'lunas' ? 'badge-selesai' : 
                                      ($detail['invoice']['status_pembayaran'] == 'batal' ? 'badge-dibatalkan' : 'badge-menunggu');
                        ?>
                        <span class="status-badge <?php echo $badge_class; ?>">
                            <?php echo ucfirst($detail['invoice']['status_pembayaran']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Metode Pembayaran</th>
                    <td><?php echo $detail['invoice']['metode_pembayaran'] ? htmlspecialchars($detail['invoice']['metode_pembayaran']) : '-'; ?></td>
                </tr>
                <?php if ($detail['invoice']['tanggal_pembayaran']): ?>
                <tr>
                    <th>Tanggal Pembayaran</th>
                    <td><?php echo date('d M Y H:i', strtotime($detail['invoice']['tanggal_pembayaran'])); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($detail['invoice']['keterangan']): ?>
                <tr>
                    <th>Keterangan</th>
                    <td><?php echo nl2br(htmlspecialchars($detail['invoice']['keterangan'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
    echo ob_get_clean();
    exit;
}
?>
    <style>
        .card-stat {
            transition: transform 0.3s;
            border-left: 4px solid;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .card-stat.total { border-left-color: #6c757d; }
        .card-stat.menunggu { border-left-color: #ffc107; }
        .card-stat.diproses { border-left-color: #0dcaf0; }
        .card-stat.selesai { border-left-color: #198754; }
        .card-stat.dibatalkan { border-left-color: #dc3545; }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .badge-menunggu { background-color: #ffc107; color: #000; }
        .badge-diproses { background-color: #0dcaf0; color: #000; }
        .badge-selesai { background-color: #198754; color: #fff; }
        .badge-dibatalkan { background-color: #dc3545; color: #fff; }
        
        .service-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .service-image:hover {
            transform: scale(1.5);
        }
    </style>
    <div class="container-fluid">
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
            <h1 class="h4">Dashboard Pelanggan</h1>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($pelanggan['nama']); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person"></i> Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </header>

        <!-- Ringkasan Status -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stat shadow-sm total">
                    <div class="card-body">
                        <h5 class="card-title">Total Service</h5>
                        <h2 class="card-text"><?php echo $total_service; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat shadow-sm menunggu">
                    <div class="card-body">
                        <h5 class="card-title">Menunggu</h5>
                        <h2 class="card-text"><?php echo $menunggu; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat shadow-sm diproses">
                    <div class="card-body">
                        <h5 class="card-title">Diproses</h5>
                        <h2 class="card-text"><?php echo $diproses; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat shadow-sm selesai">
                    <div class="card-body">
                        <h5 class="card-title">Selesai</h5>
                        <h2 class="card-text"><?php echo $selesai; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Service -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Service Saya</h5>
                    <a href="buat_service.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Buat Service Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal Masuk</th>
                                <th>Jenis Kerusakan</th>
                                <th>Status</th>
                                <th>Teknisi</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                            <tr>
                                <td>#<?php echo $service['id']; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($service['tanggal_masuk'])); ?></td>
                                <td><?php echo htmlspecialchars($service['jenis_kerusakan']); ?></td>
                                <td>
                                    <?php 
                                    $badge_class = '';
                                    switch ($service['status']) {
                                        case 'menunggu': $badge_class = 'badge-menunggu'; break;
                                        case 'diproses': $badge_class = 'badge-diproses'; break;
                                        case 'selesai': $badge_class = 'badge-selesai'; break;
                                        case 'dibatalkan': $badge_class = 'badge-dibatalkan'; break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($service['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $service['nama_teknisi'] ? htmlspecialchars($service['nama_teknisi']) : '-'; ?>
                                </td>
                                <td>
                                    <?php if ($service['foto_kerusakan']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($service['foto_kerusakan']); ?>" 
                                         alt="Foto Kerusakan" 
                                         class="service-image rounded"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#imageModal"
                                         data-img-src="uploads/<?php echo htmlspecialchars($service['foto_kerusakan']); ?>">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary detail-service" 
                                            data-service-id="<?php echo $service['id']; ?>">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Service -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Detail Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="serviceDetailContent">
                    <!-- Konten akan diisi via AJAX -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Foto Kerusakan">
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Modal gambar
            $('#imageModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var imgSrc = button.data('img-src');
                $('#modalImage').attr('src', imgSrc);
            });

            // Modal detail service
            $('.detail-service').click(function() {
                var serviceId = $(this).data('service-id');
                $('#serviceModal').modal('show');
                
                $.ajax({
                    url: 'dashboard_pelanggan.php?action=get_service_detail&id=' + serviceId,
                    type: 'GET',
                    success: function(response) {
                        $('#serviceDetailContent').html(response);
                    },
                    error: function() {
                        $('#serviceDetailContent').html('<div class="alert alert-danger">Gagal memuat detail service</div>');
                    }
                });
            });
        });
    </script>