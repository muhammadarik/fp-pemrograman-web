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

<h2>Data Kategori Produk</h2>
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
  + Tambah Kategori
</button>
<table border="1" cellpadding="10">
    <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
    <?php
    $no=1;
    while ($row = mysqli_fetch_assoc($kategori)): ?>
        <tr>
            <td><?= $no++?></td>
            <td><?= $row['nama'] ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditKategori<?= $row['id'] ?>">Edit</button>
                <a href="?page=productCategory/index&hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>

        <!-- Modal Edit di dalam loop -->
        <div class="modal fade" id="modalEditKategori<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEditKategoriLabel<?= $row['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                <h5 class="modal-title" id="modalEditKategoriLabel<?= $row['id'] ?>">Edit Kategori Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="mb-3">
                    <label for="editNamaKategori<?= $row['id'] ?>" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="editNamaKategori<?= $row['id'] ?>" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                </div>
                </div>
                <div class="modal-footer">
                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
            </div>
        </div>
        </div>

        <?php endwhile; ?>

</table>

<!-- Modal tambah kategori-->
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
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>