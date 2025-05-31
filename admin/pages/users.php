<?php
include '../config/config.php';

// tampilkan data users
$result = $conn->query("SELECT * FROM users");

// tambah user
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Validasi role
    $allowed_roles = ['admin', 'technician', 'pelanggan'];
    if (!in_array($role, $allowed_roles)) {
        echo "<script>alert('Role tidak valid!');</script>";
        exit;
    }

    // Cek apakah username sudah ada
    $checkUser = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($checkUser->num_rows > 0) {
        echo "<script>alert('Username sudah ada!');</script>";
    } else {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='?page=users';</script>";
    }
}

// hapus user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id='$id'");
    echo "<script>alert('User berhasil dihapus!'); window.location.href='?page=users';</script>";
}
?>


<div class="container">
  <h4>Data Produk</h4>
  <!-- Button trigger modal -->
<button type="button" class=" my-2 btn btn-success" data-bs-toggle="modal" data-bs-target="#userModal">Tambah User</button>
<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <tr>
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['username'] ?></td>
        <td><?= $row['role'] ?></td>
        <td>
            <a href="?page=users&delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
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

<!-- modal tambah user -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="userForm" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalLabel">Tambah/Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="userId">

          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>

          <div class="mb-3">
            <label class="form-label d-block">Role</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin" required>
              <label class="form-check-label" for="roleAdmin">Admin</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="role" id="roleTechnician" value="technician" required>
              <label class="form-check-label" for="roleTechnician">Technician</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="role" id="rolePelanggan" value="pelanggan" required>
              <label class="form-check-label" for="rolePelanggan">Pelanggan</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" name="tambah">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
