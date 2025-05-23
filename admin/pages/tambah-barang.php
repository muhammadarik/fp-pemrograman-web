<?php
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teknisi') {
    echo "Akses ditolak!";
    exit;
}
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    mysqli_query($conn, "INSERT INTO barang (nama_barang, harga) VALUES ('$nama', '$harga')");
    header("Location: index.php?page=barang");
}
?>
<form method="post">
    Nama Barang: <input type="text" name="nama"><br>
    Harga: <input type="number" name="harga"><br>
    <button name="simpan">Simpan</button>
</form>