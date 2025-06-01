<?php

// ==== Konfigurasi Pagination ====
$limit_options = [10, 20, 50, 100]; // Pilihan jumlah data per halaman
// Ambil limit dari GET, validasi, jika tidak valid pakai default 10
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limit_options) ? (int)$_GET['limit'] : 10;

// Halaman saat ini, default halaman 1
$current_page = isset($_GET['p']) ? intval($_GET['p']) : 1;
// Pastikan halaman tidak kurang dari 1
if ($current_page < 1) {
    $current_page = 1;
}

$offset = ($current_page - 1) * $limit; // Offset untuk query SQL

// ==== Handle Tambah Pelanggan ====
if (isset($_POST['tambah'])) {
    $user_id = htmlspecialchars($_POST['user_id']); // Asumsi user_id adalah string/varchar
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $no_hp = htmlspecialchars($_POST['no_hp']);

    // Query untuk insert pelanggan
    // Gunakan prepared statement untuk keamanan
    $query = "INSERT INTO pelanggan (user_id, nama, email, no_hp, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $user_id, $nama, $email, $no_hp);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = 'Pelanggan berhasil ditambahkan!';
            header('Location: ?page=pelanggan/index'); // Redirect dengan GET param
            exit();
        } else {
            $_SESSION['message'] = 'Gagal menambahkan pelanggan: ' . htmlspecialchars(mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Gagal menyiapkan statement: ' . htmlspecialchars(mysqli_error($conn));
    }
    // Jika ada pesan error, pastikan untuk tidak redirect agar alert bisa tampil
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
}

// ==== Handle Edit Pelanggan ====
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $user_id = htmlspecialchars($_POST['user_id']);
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $no_hp = htmlspecialchars($_POST['no_hp']);

    // Query update pelanggan
    // Gunakan prepared statement untuk keamanan
    $query = "UPDATE pelanggan SET
                        user_id = ?,
                        nama = ?,
                        email = ?,
                        no_hp = ?
                      WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $user_id, $nama, $email, $no_hp, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = 'Pelanggan berhasil diperbarui!';
            header('Location: ?page=pelanggan/index');
            exit();
        } else {
            $_SESSION['message'] = 'Gagal memperbarui pelanggan: ' . htmlspecialchars(mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Gagal menyiapkan statement: ' . htmlspecialchars(mysqli_error($conn));
    }
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

// ==== Handle Hapus Pelanggan ====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Query hapus pelanggan
    // Gunakan prepared statement untuk keamanan
    $query = "DELETE FROM pelanggan WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = 'Pelanggan berhasil dihapus!';
            header('Location: ?page=pelanggan/index');
            exit();
        } else {
            $_SESSION['message'] = 'Gagal menghapus pelanggan: ' . htmlspecialchars(mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Gagal menyiapkan statement: ' . htmlspecialchars(mysqli_error($conn));
    }
    echo "<script>alert('" . addslashes($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}

// ==== Ambil Data Pelanggan (disesuaikan untuk pagination) ====
$where = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . htmlspecialchars($_GET['search']) . '%';
    // Menggunakan LIKE untuk pencarian di beberapa kolom
    $where = "WHERE nama LIKE '$search_term' OR email LIKE '$search_term' OR no_hp LIKE '$search_term'";
}

// Query untuk menghitung total pelanggan (penting untuk pagination)
$total_pelanggan_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan $where");
$total_pelanggan_data = mysqli_fetch_assoc($total_pelanggan_query);
$total_records = $total_pelanggan_data['total'];

// Hitung total halaman, pastikan minimal 1 halaman meskipun tidak ada data
$total_pages = ceil($total_records / $limit);
if ($total_pages == 0 && $total_records == 0) {
    $total_pages = 1; // Jika tidak ada data, tetap setidaknya 1 halaman
}

// Pastikan current_page tidak melebihi total_pages
if ($current_page > $total_pages) {
    $current_page = $total_pages;
    // Jika total_records 0, offset tetap 0
    $offset = ($current_page - 1) * $limit;
    if ($offset < 0) $offset = 0; // Pastikan offset tidak negatif
}

// Query utama untuk mengambil data pelanggan dengan LIMIT dan OFFSET
$pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan
                                     $where
                                     ORDER BY id DESC
                                     LIMIT $limit OFFSET $offset"); // Tambahkan ORDER BY agar konsisten

// Ambil parameter URL yang ada untuk dipertahankan di link pagination
$query_params = $_GET;
unset($query_params['page']); // Hapus 'page' karena sudah ada di URL dasar
unset($query_params['p']);    // Hapus 'p' karena akan diganti di loop pagination
unset($query_params['delete']); // Hapus 'delete' agar tidak terulang
// Gunakan http_build_query untuk membuat string query URL yang aman
$base_url_params = http_build_query($query_params);
$base_url_pagination = "?page=pelanggan/index" . (!empty($base_url_params) ? "&" . $base_url_params : "");

?>

<div class="container">
    <h4>Data Pelanggan</h4>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Pelanggan</button>
        <form method="GET" class="d-flex align-items-center">
            <input type="hidden" name="page" value="pelanggan/index">
            <div class="input-group me-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pelanggan..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-outline-secondary btn-sm" type="submit">Cari</button>
            </div>
            <div>
                <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php foreach ($limit_options as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= $limit == $option ? 'selected' : '' ?>>
                            <?= htmlspecialchars($option) ?> Data
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($pelanggan) > 0):
                $no = $offset + 1; // Sesuaikan nomor urut dengan offset
                while($row = mysqli_fetch_assoc($pelanggan)):
            ?>
            <tr>
                <td><?= htmlspecialchars($no++) ?></td>
                <!-- <td><?= htmlspecialchars($row['user_id']) ?></td> -->
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                <td><?= date('d M Y H:i:s', strtotime($row['created_at'])) ?></td>
                <td>
                    <div class="d-flex">
                        <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEditPelanggan<?= htmlspecialchars($row['id']) ?>">Edit</button>
                        <a href="<?= htmlspecialchars($base_url_pagination . "&delete=" . $row['id']) ?>" onclick="return confirm('Yakin ingin hapus pelanggan ini?')" class="btn btn-danger btn-sm">Hapus</a>
                    </div>
                </td>
            </tr>
            <div class="modal fade" id="modalEditPelanggan<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="modalEditPelanggan<?= htmlspecialchars($row['id']) ?>Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditPelanggan<?= htmlspecialchars($row['id']) ?>Label">Edit Pelanggan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                                <div class="mb-3">
                                    <label for="nama_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" id="nama_edit_<?= htmlspecialchars($row['id']) ?>" value="<?= htmlspecialchars($row['nama']) ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Email</label>
                                    <input type="email" name="email" id="email_edit_<?= htmlspecialchars($row['id']) ?>" value="<?= htmlspecialchars($row['email']) ?>" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Nomor HP</label>
                                    <input type="text" name="no_hp" id="no_hp_edit_<?= htmlspecialchars($row['id']) ?>" value="<?= htmlspecialchars($row['no_hp']) ?>" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="7" class="text-center">Tidak ada data pelanggan yang ditemukan.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= htmlspecialchars($base_url_pagination) ?>&p=<?= htmlspecialchars($current_page - 1) ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $current_page == $i ? 'active' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($base_url_pagination) ?>&p=<?= htmlspecialchars($i) ?>"><?= htmlspecialchars($i) ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= htmlspecialchars($base_url_pagination) ?>&p=<?= htmlspecialchars($current_page + 1) ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>

</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="user_id" value="0">
                <div class="mb-3">
                    <label for="nama_tambah" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama_tambah" class="form-control" placeholder="Nama Pelanggan" required>
                </div>
                <div class="mb-3">
                    <label for="email_tambah" class="form-label">Email</label>
                    <input type="email" name="email" id="email_tambah" class="form-control" placeholder="Email Pelanggan" required>
                </div>
                <div class="mb-3">
                    <label for="no_hp_tambah" class="form-label">Nomor HP</label>
                    <input type="text" name="no_hp" id="no_hp_tambah" class="form-control" placeholder="Nomor HP">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php
mysqli_close($conn);
?>