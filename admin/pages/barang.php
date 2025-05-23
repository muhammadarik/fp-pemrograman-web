<?php
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$data = mysqli_query($conn, "SELECT * FROM barang LIMIT $mulai, $batas");
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$pages = ceil($total / $batas);
?>

<a href="?page=tambah-barang">Tambah Barang</a>
<table border="1">
<tr><th>Nama</th><th>Harga</th><th>Aksi</th></tr>
<?php while($d = mysqli_fetch_assoc($data)) { ?>
<tr>
    <td><?= $d['nama_barang'] ?></td>
    <td><?= $d['harga'] ?></td>
    <td>
        <a href='?page=edit-barang&id=<?= $d['id'] ?>'>Edit</a>
        <a href='?page=hapus-barang&id=<?= $d['id'] ?>'>Hapus</a>
    </td>
</tr>
<?php } ?>
</table>

<div class='pagination'>
<?php for ($i = 1; $i <= $pages; $i++) { ?>
    <a href="?page=barang&halaman=<?= $i ?>"><?= $i ?></a>
<?php } ?>
</div>