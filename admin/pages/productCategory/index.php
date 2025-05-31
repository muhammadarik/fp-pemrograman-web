<?php
include "../config/config.php";

// get data kategori dari database
$kategori = mysqli_query($conn, "SELECT * FROM product_category");

// Fungsi tambah kategori
if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $query = "INSERT INTO product_category (nama) VALUES ('$nama')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Kategori berhasil ditambahkan!');
            window.location.href='?page=productCategory/index';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan kategori: " . mysqli_error($conn) . "</div>";
    }
}

// Fungsi update kategori
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];

    $query = "UPDATE product_category SET nama='$nama' WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Kategori berhasil diperbarui!');
            window.location.href='?page=productCategory/index';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui kategori: " . mysqli_error($conn) . "</div>";
    }
}

// Fungsi hapus langsung dari index.php jika ada parameter 'hapus'
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $hapus = mysqli_query($conn, "DELETE FROM product_category WHERE id=$id");
 
    if ($hapus) {
        echo "<script>
            alert('Kategori berhasil dihapus!');
            window.location.href='?page=productCategory/index';
        </script>";
    } else {
    echo "<div class='alert alert-danger'>Gagal menghapus kategori: " . mysqli_error($conn) . "</div>";}
    echo "GET Hapus: " . $_GET['hapus'];
}
?>

<div class="container py-4">
    <h2 class="mb-4">Data Kategori Produk</h2>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
        <i class="bi bi-plus-circle me-1"></i> + Tambah Kategori
    </button>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($kategori) > 0) {
                    while ($row = mysqli_fetch_assoc($kategori)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEditKategori<?= $row['id'] ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="?page=productCategory/index&hapus=<?= htmlspecialchars($row['id']) ?>" 
                                   onclick="return confirm('Yakin ingin menghapus kategori ini? Data produk yang terkait mungkin terpengaruh.')" 
                                   class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditKategori<?= htmlspecialchars($row['id']) ?>" tabindex="-1" 
                             aria-labelledby="modalEditKategoriLabel<?= htmlspecialchars($row['id']) ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalEditKategoriLabel<?= htmlspecialchars($row['id']) ?>">Edit Kategori Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                            <div class="mb-3">
                                                <label for="editNamaKategori<?= htmlspecialchars($row['id']) ?>" class="form-label">Nama Kategori</label>
                                                <input type="text" class="form-control" id="editNamaKategori<?= htmlspecialchars($row['id']) ?>" 
                                                       name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                } else { ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data kategori yang ditemukan.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-labelledby="modalTambahKategoriLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKategoriLabel">Tambah Kategori Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaKategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="namaKategori" name="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>