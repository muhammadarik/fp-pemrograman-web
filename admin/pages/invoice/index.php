<?php
// session_start();
include_once __DIR__ . '/../../../config/config.php';

// Pastikan koneksi database tersedia
if (!isset($conn)) {
    die("Koneksi database gagal. Pastikan config.php sudah benar.");
}

// Tambah Invoice
if (isset($_POST['submit_invoice'])) {
    $service_id = $_POST['service_id'];
    $total_biaya = $_POST['total_biaya'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $keterangan = $_POST['keterangan'] ?? '';
    $admin_id = $_SESSION['user_id'];

    $tanggal_invoice = date('Y-m-d H:i:s');
    $tanggal_pembayaran = ($status_pembayaran === 'lunas') ? $tanggal_invoice : null;

    $query = "INSERT INTO invoice (service_id, tanggal_invoice, total_biaya, keterangan, metode_pembayaran, status_pembayaran, tanggal_pembayaran, id_admin_pembuat, created_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isdssssi", $service_id, $tanggal_invoice, $total_biaya, $keterangan, $metode_pembayaran, $status_pembayaran, $tanggal_pembayaran, $admin_id);
    $stmt->execute();
    header("Location: ../../index.php?page=invoice/index");
    exit;
}

// Update Invoice
if (isset($_POST['update_invoice'])) {
    $id = $_POST['id'];
    $total_biaya = $_POST['total_biaya'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $keterangan = $_POST['keterangan'] ?? '';
    $tanggal_pembayaran = ($status_pembayaran === 'lunas') ? date('Y-m-d H:i:s') : null;

    $query = "UPDATE invoice SET total_biaya=?, metode_pembayaran=?, keterangan=?, status_pembayaran=?, tanggal_pembayaran=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dssssi", $total_biaya, $metode_pembayaran, $keterangan, $status_pembayaran, $tanggal_pembayaran, $id);
    $stmt->execute();
    header("Location: ../../index.php?page=invoice/index");
    exit;
}

// Hapus Invoice
if (isset($_POST['hapus_id'])) {
    $id = $_POST['hapus_id'];
    $conn->query("DELETE FROM invoice WHERE id = $id");
    header("Location: ../../index.php?page=invoice/index");
    exit;
}

// Ambil semua invoice
$query = "SELECT i.*, s.jenis_kerusakan, s.deskripsi, u.username AS admin_pembuat
          FROM invoice i
          LEFT JOIN service s ON i.service_id = s.id
          LEFT JOIN users u ON i.id_admin_pembuat = u.id
          ORDER BY i.tanggal_invoice DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo "<b>SQL Error (Invoice):</b> " . mysqli_error($conn);
    exit;
}

// Ambil semua service yang belum punya invoice & status 'selesai'
$service_query = "SELECT s.*, p.nama AS nama_pelanggan
                  FROM service s
                  JOIN pelanggan p ON s.id_pelanggan = p.id
                  WHERE s.status = 'selesai' 
                  AND NOT EXISTS (SELECT 1 FROM invoice WHERE invoice.service_id = s.id)";

$service_result = mysqli_query($conn, $service_query);

if (!$service_result) {
    echo "<b>SQL Error (Service):</b> " . mysqli_error($conn);
    exit;
}
?>

<div class="container mt-4">
    <h3>Manajemen Invoice</h3>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Invoice</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Service</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
                <th>Pembuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; while($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['jenis_kerusakan'] ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_invoice'])) ?></td>
                <td>Rp<?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
                <td><?= ucfirst($row['status_pembayaran']) ?></td>
                <td><?= $row['admin_pembuat'] ?></td>
                <td>
                    <a href="pages/invoice/cetak.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-primary">Cetak</a>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">Edit</button>
                    <form method="post" action="pages/invoice/index.php" style="display:inline;">
                        <input type="hidden" name="hapus_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post" action="pages/invoice/index.php">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">Edit Invoice</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Total Biaya</label>
                                <input type="number" name="total_biaya" class="form-control" value="<?= $row['total_biaya'] ?>" required>
                                <label>Metode Pembayaran</label>
                                <input type="text" name="metode_pembayaran" class="form-control" value="<?= $row['metode_pembayaran'] ?>">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control"><?= $row['keterangan'] ?></textarea>
                                <label>Status Pembayaran</label>
                                <select name="status_pembayaran" class="form-select">
                                    <option <?= $row['status_pembayaran']=='pending'?'selected':'' ?>>pending</option>
                                    <option <?= $row['status_pembayaran']=='lunas'?'selected':'' ?>>lunas</option>
                                    <option <?= $row['status_pembayaran']=='batal'?'selected':'' ?>>batal</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" name="update_invoice">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="pages/invoice/index.php">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Service</label>
                    <select name="service_id" class="form-select" required>
                        <option disabled selected>Pilih layanan</option>
                        <?php while($s = mysqli_fetch_assoc($service_result)): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= $s['nama_pelanggan'] ?> - <?= $s['jenis_kerusakan'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label>Total Biaya</label>
                    <input type="number" name="total_biaya" class="form-control" required>
                    <label>Metode Pembayaran</label>
                    <input type="text" name="metode_pembayaran" class="form-control">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control"></textarea>
                    <label>Status Pembayaran</label>
                    <select name="status_pembayaran" class="form-select">
                        <option value="pending" selected>pending</option>
                        <option value="lunas">lunas</option>
                        <option value="batal">batal</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" name="submit_invoice">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
