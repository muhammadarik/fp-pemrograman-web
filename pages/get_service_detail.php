<?php
// session_start();
// require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    die(json_encode(['error' => 'Unauthorized']));
}

if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'ID Service tidak valid']));
}

$service_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

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
    die('<div class="alert alert-danger">Service tidak ditemukan</div>');
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
?>

<div class="row">
    <div class="col-md-6">
        <h5>Informasi Service</h5>
        <table class="table table-sm">
            <tr>
                <th width="40%">ID Service</th>
                <td>#<?php echo $service['id']; ?></td>
            </tr>
            <tr>
                <th>Pelanggan</th>
                <td><?php echo htmlspecialchars($service['nama_pelanggan']); ?></td>
            </tr>
            <tr>
                <th>Tanggal Masuk</th>
                <td><?php echo date('d M Y H:i', strtotime($service['tanggal_masuk'])); ?></td>
            </tr>
            <tr>
                <th>Tanggal Update</th>
                <td><?php echo date('d M Y H:i', strtotime($service['tanggal_update'])); ?></td>
            </tr>
            <tr>
                <th>Teknisi</th>
                <td><?php echo $service['nama_teknisi'] ? htmlspecialchars($service['nama_teknisi']) : '-'; ?></td>
            </tr>
            <tr>
                <th>Status</th>
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
            </tr>
        </table>

        <h5 class="mt-4">Detail Kerusakan</h5>
        <p><strong>Jenis Kerusakan:</strong><br>
        <?php echo htmlspecialchars($service['jenis_kerusakan']); ?></p>
        
        <p><strong>Deskripsi:</strong><br>
        <?php echo nl2br(htmlspecialchars($service['deskripsi'])); ?></p>
        
        <?php if ($service['foto_kerusakan']): ?>
        <p><strong>Foto Kerusakan:</strong><br>
        <img src="uploads/<?php echo htmlspecialchars($service['foto_kerusakan']); ?>" 
             class="img-fluid rounded mt-2" style="max-height: 200px;"></p>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h5>Riwayat Status</h5>
        <div class="timeline">
            <?php if (count($history) > 0): ?>
                <?php foreach ($history as $item): ?>
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

        <?php if ($invoice): ?>
        <h5 class="mt-4">Informasi Invoice</h5>
        <table class="table table-sm">
            <tr>
                <th width="40%">Tanggal Invoice</th>
                <td><?php echo date('d M Y H:i', strtotime($invoice['tanggal_invoice'])); ?></td>
            </tr>
            <tr>
                <th>Total Biaya</th>
                <td>Rp <?php echo number_format($invoice['total_biaya'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Status Pembayaran</th>
                <td>
                    <?php 
                    $badge_class = $invoice['status_pembayaran'] == 'lunas' ? 'badge-selesai' : 
                                  ($invoice['status_pembayaran'] == 'batal' ? 'badge-dibatalkan' : 'badge-menunggu');
                    ?>
                    <span class="status-badge <?php echo $badge_class; ?>">
                        <?php echo ucfirst($invoice['status_pembayaran']); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Metode Pembayaran</th>
                <td><?php echo $invoice['metode_pembayaran'] ? htmlspecialchars($invoice['metode_pembayaran']) : '-'; ?></td>
            </tr>
            <?php if ($invoice['tanggal_pembayaran']): ?>
            <tr>
                <th>Tanggal Pembayaran</th>
                <td><?php echo date('d M Y H:i', strtotime($invoice['tanggal_pembayaran'])); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($invoice['keterangan']): ?>
            <tr>
                <th>Keterangan</th>
                <td><?php echo nl2br(htmlspecialchars($invoice['keterangan'])); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php endif; ?>
    </div>
</div>