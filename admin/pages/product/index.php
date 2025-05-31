<?php
include '../config/config.php';

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

// Contoh: $_SERVER['DOCUMENT_ROOT'] . '/fp-pemrograman-web/assets/image/';
$upload_dir_server = $_SERVER['DOCUMENT_ROOT'] . '/assets/image/';

// Ini adalah URL dasar untuk memanggil gambar dari browser.
$upload_dir_url = '/assets/image/';

// Pastikan folder upload ada dan memiliki izin tulis (penting untuk upload gambar)
if (!is_dir($upload_dir_server)) {
    if (!mkdir($upload_dir_server, 0755, true)) { // 0755 adalah izin yang disarankan
        echo "<div class='alert alert-danger'>Gagal membuat folder upload: " . htmlspecialchars($upload_dir_server) . ". Periksa izin direktori.</div>";
        // Tidak exit di sini, karena ini hanya check, proses bisa lanjut jika tidak ada upload
    }
}

// ==== Handle Tambah Produk ====
if (isset($_POST['tambah'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $harga = htmlspecialchars($_POST['harga']);
    $catg_id = intval($_POST['catg_id']);
    $status = htmlspecialchars($_POST['status']); 
    
    // pembatasan ekstensi file gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!empty($gambar) && !in_array($ext, $allowed_ext)) { // Tambahkan cek !empty($gambar)
        echo "<div class='alert alert-danger'>Format file tidak didukung. Hanya file gambar yang diizinkan.</div>";
        exit;
    }

    $gambar_nama_unik = ''; // Inisialisasi default
    if (!empty($gambar)) { // Hanya proses upload jika ada file yang dipilih
        $gambar_nama_unik = uniqid() . '.' . $ext; // Generate nama unik
        $target_file = $upload_dir_server . $gambar_nama_unik;

        // Pindahkan file ke folder tujuan
        if (!move_uploaded_file($tmp, $target_file)) {
            echo "<div class='alert alert-danger'>Gagal mengupload gambar.</div>";
            exit; // Hentikan eksekusi jika gagal upload gambar
        }
    }

    // Saya akan tetap pakai mysqli_query seperti di kode asli Anda, tapi sangat disarankan pakai prepared statement.
    $query = "INSERT INTO produk (nama, harga, catg_id, status, gambar) VALUES ('$nama', '$harga', '$catg_id', '$status', '$gambar_nama_unik')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Produk berhasil ditambahkan!');
            window.location.href='?page=product/index';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan produk: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
    }
}

// ==== Handle Edit Produk ====
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']); // Pastikan ID adalah integer
    $nama = htmlspecialchars($_POST['nama']);
    $harga = htmlspecialchars($_POST['harga']);
    $catg_id = intval($_POST['catg_id']);
    $status = htmlspecialchars($_POST['status']);

    // pembatasan ekstensi file gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Ambil gambar lama dari database
    $result_old_img = mysqli_query($conn, "SELECT gambar FROM produk WHERE id='$id'");
    $old_data = mysqli_fetch_assoc($result_old_img);
    $old_gambar = $old_data['gambar'];

    $gambar_untuk_db = $old_gambar; // Default: gunakan gambar lama

    // Jika user mengupload gambar baru
    if (!empty($gambar)) {
        if (!in_array($ext, $allowed_ext)) {
            echo "<div class='alert alert-danger'>Format file tidak didukung. Hanya file gambar yang diizinkan.</div>";
            exit;
        }
        $gambar_baru_nama_unik = uniqid() . '.' . $ext; // Generate nama unik untuk gambar baru
        $target_file_baru = $upload_dir_server . $gambar_baru_nama_unik;

        // Upload gambar baru
        if (!move_uploaded_file($tmp, $target_file_baru)) {
            echo "<div class='alert alert-danger'>Gagal mengupload gambar baru.</div>";
            exit;
        }

        // Hapus gambar lama jika ada dan berbeda dari yang baru diupload
        if (!empty($old_gambar) && file_exists($upload_dir_server . $old_gambar) && $old_gambar !== $gambar_baru_nama_unik) {
            unlink($upload_dir_server . $old_gambar);
        }
        $gambar_untuk_db = $gambar_baru_nama_unik; // Update nama gambar untuk disimpan ke DB
    }

    // Query update
    $query = "UPDATE produk SET 
                      nama='$nama', 
                      harga='$harga', 
                      catg_id='$catg_id', 
                      status='$status', 
                      gambar='$gambar_untuk_db' 
                    WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Produk berhasil diperbarui!');
            window.location.href='?page=product/index';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui produk: " . htmlspecialchars(mysqli_error($conn)) . "</div>";
    }
}

// Fungsi hapus langsung dari index.php jika ada parameter 'hapus'
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Ambil nama file gambar untuk dihapus dari server sebelum menghapus record dari DB
    $result_img = mysqli_query($conn, "SELECT gambar FROM produk WHERE id=$id");
    $data_img = mysqli_fetch_assoc($result_img);
    $gambar_to_delete = $data_img['gambar'];

    $delete = mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
   
    if ($delete) {
        // Hapus file gambar dari server jika ada
        if (!empty($gambar_to_delete) && file_exists($upload_dir_server . $gambar_to_delete)) {
            unlink($upload_dir_server . $gambar_to_delete);
        }

        echo "<script>
            alert('Produk berhasil dihapus!');
            window.location.href='?page=product/index';
        </script>";
    } else {
    echo "<div class='alert alert-danger'>Gagal menghapus produk: " . htmlspecialchars(mysqli_error($conn)) . "</div>";}
}

// ==== Ambil Data Produk dan Kategori (disesuaikan untuk pagination) ====
$where = '';
if (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] !== '') {
    $filter_id = intval($_GET['filter_kategori']);
    $where = "WHERE produk.catg_id = $filter_id";
}

// Query untuk menghitung total produk (penting untuk pagination)
$total_produk_query = $conn->query("SELECT COUNT(*) AS total FROM produk LEFT JOIN product_category ON produk.catg_id = product_category.id $where");
$total_produk_data = $total_produk_query->fetch_assoc();
$total_records = $total_produk_data['total'];

// Hitung total halaman, pastikan minimal 1 halaman meskipun tidak ada data
$total_pages = ceil($total_records / $limit); 
if ($total_pages == 0 && $total_records == 0) {
    $total_pages = 1; // Jika tidak ada data, tetap setidaknya 1 halaman
}

// Pastikan current_page tidak melebihi total_pages
if ($current_page > $total_pages) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $limit; // Hitung ulang offset
}


// Query utama untuk mengambil data produk dengan LIMIT dan OFFSET
$produk = $conn->query("SELECT produk.*, product_category.nama AS kategori FROM produk 
                             LEFT JOIN product_category ON produk.catg_id = product_category.id
                             $where
                             ORDER BY produk.id DESC 
                             LIMIT $limit OFFSET $offset"); // Tambahkan ORDER BY agar konsisten

$kategori = $conn->query("SELECT * FROM product_category ORDER BY nama ASC"); // Order kategori berdasarkan nama

// Ambil parameter URL yang ada untuk dipertahankan di link pagination
$query_params = $_GET;
unset($query_params['page']); // Hapus 'page' karena sudah ada di URL dasar
unset($query_params['p']);    // Hapus 'p' karena akan diganti di loop pagination
// Gunakan http_build_query untuk membuat string query URL yang aman
$base_url_params = http_build_query($query_params);
$base_url_pagination = "?page=product/index" . (!empty($base_url_params) ? "&" . $base_url_params : "");

?>

<div class="container">
    <h4>Data Produk</h4>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Produk</button>
        <form method="GET" class="d-flex align-items-center">
            <input type="hidden" name="page" value="product/index">
            <div class="me-2">
                <select name="filter_kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= htmlspecialchars($k['id']) ?>" <?= isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th><th>Nama</th><th>Harga</th><th>Gambar</th><th>Kategori</th><th>Status</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($produk->num_rows > 0):
                $no = $offset + 1; // Sesuaikan nomor urut dengan offset
                while($row = $produk->fetch_assoc()): 
            ?>
            <tr>
                <td><?= htmlspecialchars($no++) ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td>Rp<?= number_format(htmlspecialchars($row['harga']), 0, ',', '.') ?></td>
                <td>
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="<?= htmlspecialchars($upload_dir_url . $row['gambar']) ?>" class="img-thumbnail img-fluid" width="100" alt="Gambar Produk">
                    <?php else: ?>
                        Tidak ada gambar
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td><?= $row['status'] == '1' ? 'Aktif' : 'Nonaktif' ?></td>
                <td>
                    <div class="d-flex">
                        <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEditProduct<?= htmlspecialchars($row['id']) ?>">Edit</button>
                        <a href="<?= htmlspecialchars($base_url_pagination . "&delete=" . $row['id']) ?>" onclick="return confirm('Yakin ingin hapus produk ini?')" class="btn btn-danger btn-sm">Hapus</a>
                    </div>     
                </td>
            </tr>
            <div class="modal fade" id="modalEditProduct<?= htmlspecialchars($row['id']) ?>" tabindex="-1" aria-labelledby="modalEditProduct<?= htmlspecialchars($row['id']) ?>Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditProduct<?= htmlspecialchars($row['id']) ?>Label">Edit Produk</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                <div class="mb-3 col-6">
                                        <label for="nama_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Nama Produk</label>
                                        <input type="text" name="nama" id="nama_edit_<?= htmlspecialchars($row['id']) ?>" value="<?= htmlspecialchars($row['nama']) ?>" class="form-control" required>
                                </div>
                                <div class="mb-3 col-6">
                                        <label for="harga_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Harga</label>
                                        <input type="number" name="harga" id="harga_edit_<?= htmlspecialchars($row['id']) ?>" value="<?= htmlspecialchars($row['harga']) ?>" class="form-control" required>
                                </div>
                                </div>
                                <div class="mb-3">
                                        <label class="form-label">Kategori:</label><br>
                                        <?php 
                                        // Ambil ulang kategori agar modal edit memiliki daftar kategori terbaru
                                        $kategori_edit_modal = $conn->query("SELECT * FROM product_category ORDER BY nama ASC"); // Ambil ulang data kategori
                                        foreach ($kategori_edit_modal as $k_edit): 
                                        ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="catg_id" id="cat_edit_<?= htmlspecialchars($row['id'] . '_' . $k_edit['id']) ?>" value="<?= htmlspecialchars($k_edit['id']) ?>" <?= $k_edit['id'] == $row['catg_id'] ? 'checked' : '' ?> required>
                                            <label class="form-check-label" for="cat_edit_<?= htmlspecialchars($row['id'] . '_' . $k_edit['id']) ?>"><?= htmlspecialchars($k_edit['nama']) ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                </div>
                                <div class="mb-3">
                                        <label for="gambar_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Gambar Produk</label>
                                        <input type="file" name="gambar" id="gambar_edit_<?= htmlspecialchars($row['id']) ?>" class="form-control">
                                        <?php if (!empty($row['gambar'])): ?>
                                        <div class="mt-2">
                                            <img src="<?= htmlspecialchars($upload_dir_url . $row['gambar']) ?>" width="120" class="img-thumbnail" alt="Gambar saat ini">
                                            <p class="text-muted" style="font-size: 0.8em;">Gambar saat ini</p>
                                        </div>
                                        <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                <label for="status_edit_<?= htmlspecialchars($row['id']) ?>" class="form-label">Status</label>
                                <select name="status" id="status_edit_<?= htmlspecialchars($row['id']) ?>" class="form-select">
                                    <option value="1" <?= $row['status'] == '1' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $row['status'] == '0' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
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
                <td colspan="7" class="text-center">Tidak ada data produk yang ditemukan.</td>
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
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama_tambah" class="form-label">Nama Produk</label>
                    <input type="text" name="nama" id="nama_tambah" class="form-control" placeholder="Nama Produk" required>
                </div>
                <div class="mb-3">
                    <label for="harga_tambah" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga_tambah" class="form-control" placeholder="Harga" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori:</label><br>
                    <?php 
                    // Ambil ulang kategori untuk modal tambah agar daftar kategori selalu terbaru
                    $kategori_tambah_modal = $conn->query("SELECT * FROM product_category ORDER BY nama ASC");
                    foreach ($kategori_tambah_modal as $k): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="catg_id" id="cat_tambah_<?= htmlspecialchars($k['id']) ?>" value="<?= htmlspecialchars($k['id']) ?>" required>
                            <label class="form-check-label" for="cat_tambah_<?= htmlspecialchars($k['id']) ?>"><?= htmlspecialchars($k['nama']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mb-3">
                    <label for="gambar_tambah" class="form-label">Gambar Produk</label>
                    <input type="file" name="gambar" id="gambar_tambah" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="status_tambah" class="form-label">Status</label>
                    <select name="status" id="status_tambah" class="form-control">
                        <option value="1" selected>Aktif</option> 
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>