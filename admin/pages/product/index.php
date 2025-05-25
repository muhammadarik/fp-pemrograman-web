<?php
include '../config/config.php';

// ==== Handle Tambah Produk ====
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $catg_id = $_POST['catg_id'];
    $status = $_POST['status'];
    
    // pembatasan ekstensi file gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed_ext)) {
        echo "<div class='alert alert-danger'>Format file tidak didukung. Hanya file gambar yang diizinkan.</div>";
        exit;
    }

    // Tentukan folder upload
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/image/';

    // Pastikan folder tujuan ada
    if (!is_dir($upload_dir)) {mkdir($upload_dir, 0755, true);}

    if ($gambar != '') {
        // Pindahkan file ke folder tujuan
        if (!move_uploaded_file($tmp, $upload_dir . $gambar)) {
            echo "<div class='alert alert-danger'>Gagal mengupload gambar.</div>";
            exit; // Hentikan eksekusi jika gagal upload gambar
        }
    }

    // Query insert
    $query = "INSERT INTO produk (nama, harga, catg_id, status, gambar) VALUES ('$nama', '$harga', '$catg_id', '$status', '$gambar')";

    // Gunakan $query, bukan $tambah (variable yang salah)
    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Produk berhasil ditambahkan!');
            window.location.href='?page=product/index';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan produk: " . mysqli_error($conn) . "</div>";
    }
}

// ==== Handle Edit Produk ====
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $catg_id = $_POST['catg_id'];
    $status = $_POST['status'];

    // pembatasan ekstensi file gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed_ext)) {
        echo "<div class='alert alert-danger'>Format file tidak didukung. Hanya file gambar yang diizinkan.</div>";
        exit;
    }

    move_uploaded_file($tmp, $upload_dir . $gambar);

    // Path upload konsisten dengan tambah produk
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/image/';

    // Ambil gambar lama dari database
    $result = mysqli_query($conn, "SELECT gambar FROM produk WHERE id='$id'");
    $old_data = mysqli_fetch_assoc($result);
    $old_gambar = $old_data['gambar'];

    // Jika user mengupload gambar baru
    if (!empty($gambar)) {
        // Upload gambar baru
        if (!move_uploaded_file($tmp, $upload_dir . $gambar)) {
            echo "<div class='alert alert-danger'>Gagal mengupload gambar.</div>";
            exit;
        }

        // Hapus gambar lama jika ada dan berbeda
        if (!empty($old_gambar) && file_exists($upload_dir . $old_gambar)) {unlink($upload_dir . $old_gambar);}

        // Query update dengan gambar baru
        $query = "UPDATE produk SET 
                    nama='$nama', 
                    harga='$harga', 
                    catg_id='$catg_id', 
                    status='$status', 
                    gambar='$gambar' 
                  WHERE id='$id'";
    } else {
        // Query update tanpa gambar
        $query = "UPDATE produk SET 
                    nama='$nama', 
                    harga='$harga', 
                    catg_id='$catg_id', 
                    status='$status' 
                  WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('Produk berhasil diperbarui!');
            window.location.href='?page=product/index';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui produk: " . mysqli_error($conn) . "</div>";
    }
}

// Fungsi hapus langsung dari index.php jika ada parameter 'hapus'
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
 
    if ($delete) {
        echo "<script>
            alert('Produk berhasil dihapus!');
            window.location.href='?page=product/index';
        </script>";
    } else {
    echo "<div class='alert alert-danger'>Gagal menghapus produk: " . mysqli_error($conn) . "</div>";}
    echo "GET Hapus: " . $_GET['delete'];
}

// ==== Ambil Data Produk dan Kategori ====
$where = '';
if (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] !== '') {
    $filter_id = intval($_GET['filter_kategori']);
    $where = "WHERE produk.catg_id = $filter_id";
}

$produk = $conn->query("SELECT produk.*, product_category.nama AS kategori FROM produk 
                        LEFT JOIN product_category ON produk.catg_id = product_category.id
                        $where");

$kategori = $conn->query("SELECT * FROM product_category");

?>

<div class="container">
  <h4>Data Produk</h4>
  <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Produk</button>
  <!-- filter kategori -->
  <form method="GET" class="row mb-3">
  <input type="hidden" name="page" value="product/index">
  <div class="col-md-4">
    <select name="filter_kategori" class="form-select" onchange="this.form.submit()">
      <option value="">-- Semua Kategori --</option>
      <?php foreach ($kategori as $k): ?>
        <option value="<?= $k['id'] ?>" <?= isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == $k['id'] ? 'selected' : '' ?>>
          <?= $k['nama'] ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th><th>Nama</th><th>Harga</th><th>Gambar</th><th>Kategori</th><th>Status</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row = $produk->fetch_assoc()): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['nama'] ?></td>
        <td><?= number_format($row['harga']) ?></td>
        <td><img src="../../assets/image/<?= $row['gambar'] ?>" class="img-thumbnail img-fluid table-image" width="50"></td>
        <td><?= $row['kategori'] ?></td>
        <td><?= $row['status'] == '1' ? 'Aktif' : 'Nonaktif' ?></td>
        <td>
            <div class="col-lg-12">
                <button class="btn btn-warning btn-sm my-2" data-bs-toggle="modal" data-bs-target="#modalEditProduct<?= $row['id'] ?>">Edit</button>
                <a href="?page=product/index&delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
            </div>      
        </td>
      </tr>
      <!-- Modal Edit di dalam loop -->
        <div class="modal fade" id="modalEditProduct<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEditProduct<?= $row['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="modal-header">
                <h5 class="modal-title" id="modalEditProduct<?= $row['id'] ?>">Edit Kategori Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                <input type="text" name="nama" value="<?= $row['nama'] ?>" class="form-control mb-2" required>
                <input type="number" name="harga" value="<?= $row['harga'] ?>" class="form-control mb-2" required>
                <!-- Kategori Radio Button -->
                    <div class="mb-2">
                    <label class="form-label">Kategori:</label><br>
                    <?php foreach ($kategori as $k): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="catg_id" id="cat<?= $row['id'] . '_' . $k['id'] ?>" value="<?= $k['id'] ?>" <?= $k['id'] == $row['catg_id'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cat<?= $row['id'] . '_' . $k['id'] ?>"><?= $k['nama'] ?></label>
                    </div>
                    <?php endforeach; ?>
                    </div>
                <input type="file" name="gambar" class="form-control mb-2">
                <?php if (!empty($row['gambar'])): ?>
                <div class="mb-2">
                    <img src="/assets/image/<?= $row['gambar'] ?>" width="80">
                    <p class="text-muted" style="font-size: 0.8em;">Gambar saat ini</p>
                </div>
                <?php endif; ?>
                <select name="status" class="form-control">
                    <option value="1" <?= $row['status'] == '1' ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= $row['status'] == '0' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
                </div>
                <div class="modal-footer">
                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> -->
                </div>
            </form>
            </div>
        </div>
        </div>

      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</div>


<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambah">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header"><h5>Tambah Produk</h5></div>
      <div class="modal-body">
        <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Produk" required>
        <input type="number" name="harga" class="form-control mb-2" placeholder="Harga" required>
        <!-- Kategori Radio Button -->
        <div class="mb-2">
          <label class="form-label">Kategori:</label><br>
          <?php foreach ($kategori as $k): ?>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="catg_id" id="cat<?= $k['id'] ?>" value="<?= $k['id'] ?>" required>
              <label class="form-check-label" for="cat<?= $k['id'] ?>"><?= $k['nama'] ?></label>
            </div>
          <?php endforeach; ?>
        </div>
        <input type="file" name="gambar" class="form-control mb-2">
        <select name="status" class="form-control">
          <option value="1">Aktif</option>
          <option value="0">Nonaktif</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" name="tambah">Simpan</button>
      </div>
    </form>
  </div>
</div>