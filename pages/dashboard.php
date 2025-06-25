<?php
// session_start();
// require_once 'config.php';

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pelanggan
$query_pelanggan = "SELECT p.* FROM pelanggan p JOIN users u ON p.user_id = u.id WHERE u.id = ?";
$stmt_pelanggan = $conn->prepare($query_pelanggan);
$stmt_pelanggan->bind_param("i", $user_id);
$stmt_pelanggan->execute();
$pelanggan = $stmt_pelanggan->get_result()->fetch_assoc();

// Ambil service
$query_service = "SELECT s.*, u.username as nama_teknisi 
                 FROM service s 
                 LEFT JOIN users u ON s.id_teknisi = u.id 
                 WHERE s.id_pelanggan = ? 
                 ORDER BY s.tanggal_masuk DESC";
$stmt_service = $conn->prepare($query_service);
$stmt_service->bind_param("i", $pelanggan['id']);
$stmt_service->execute();
$services = $stmt_service->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 1055;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            overflow-y: auto;
        }
        .custom-modal:target {
            display: block;
        }
        .custom-modal .modal-dialog {
            margin: 50px auto;
            max-width: 800px;
        }
    </style>
</head>
<!-- <body class="bg-light"> -->
<div class="container py-4">
    <h3>Selamat Datang, <?= htmlspecialchars($pelanggan['nama']) ?></h3>
    <hr>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th><th>Jenis Kerusakan</th><th>Status</th><th>Teknisi</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; foreach ($services as $s): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($s['jenis_kerusakan']) ?></td>
                <td><?= $s['status'] ?></td>
                <td><?= $s['nama_teknisi'] ?? '-' ?></td>
                <td>
                    <a href="#modal-<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php foreach ($services as $s): ?>
<div id="modal-<?= $s['id'] ?>" class="custom-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <a href="#" class="btn-close position-absolute end-0 m-3"></a>
      <div class="modal-header">
        <h5 class="modal-title">Detail Service</h5>
      </div>
      <div class="modal-body">
        <h6>Informasi Service</h6>
        <table class="table table-bordered small">
          <tr><th>ID</th><td><?= $s['id'] ?></td></tr>
          <tr><th>Kerusakan</th><td><?= htmlspecialchars($s['jenis_kerusakan']) ?></td></tr>
          <tr><th>Deskripsi</th><td><?= nl2br(htmlspecialchars($s['deskripsi'])) ?></td></tr>
          <tr><th>Status</th><td><?= ucfirst($s['status']) ?></td></tr>
        </table>

        <?php
        $stmt = $conn->prepare("SELECT hs.*, u.username as updated_by_name FROM history_status hs LEFT JOIN users u ON hs.updated_by = u.id WHERE hs.service_id = ? ORDER BY hs.updated_on DESC");
        $stmt->bind_param("i", $s['id']);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>

        <h6>Riwayat Status</h6>
        <table class="table table-bordered table-sm small">
          <thead><tr><th>Status</th><th>Oleh</th><th>Waktu</th><th>Catatan</th></tr></thead>
          <tbody>
          <?php foreach ($history as $h): ?>
            <tr>
              <td><?= $h['status'] ?></td>
              <td><?= $h['updated_by_name'] ?? 'System' ?></td>
              <td><?= date('d-m-Y H:i', strtotime($h['updated_on'])) ?></td>
              <td><?= nl2br(htmlspecialchars($h['catatan'])) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

        <?php
        $stmt = $conn->prepare("SELECT * FROM invoice WHERE service_id = ?");
        $stmt->bind_param("i", $s['id']);
        $stmt->execute();
        $invoice = $stmt->get_result()->fetch_assoc();
        ?>
        <?php if ($invoice): ?>
        <h6>Invoice</h6>
        <table class="table table-bordered small">
          <tr><th>ID</th><td><?= 'INV-' . $invoice['id'] ?></td></tr>
          <tr><th>Tanggal</th><td><?= date('d-m-Y H:i', strtotime($invoice['tanggal_invoice'])) ?></td></tr>
          <tr><th>Total</th><td>Rp<?= number_format($invoice['total_biaya'], 0, ',', '.') ?></td></tr>
          <tr><th>Pembayaran</th><td><?= htmlspecialchars($invoice['metode_pembayaran']) ?></td></tr>
          <tr><th>Status</th><td><?= ucfirst($invoice['status_pembayaran']) ?></td></tr>
          <tr><th>Keterangan</th><td><?= nl2br(htmlspecialchars($invoice['keterangan'])) ?></td></tr>
        </table>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-secondary">Tutup</a>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>
<!-- </body>
</html> -->
