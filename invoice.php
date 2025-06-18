<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "toko_servis";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id_transaksi = $_GET['id'];
$sql_transaksi = "SELECT t.*, p.nama, p.alamat, p.telepon 
                  FROM transaksi t 
                  JOIN pelanggan p ON t.pelanggan_id = p.id 
                  WHERE t.id = $id_transaksi";
$result_transaksi = $conn->query($sql_transaksi);
$data_transaksi = $result_transaksi->fetch_assoc();

$sql_detail = "SELECT * FROM detail_transaksi WHERE transaksi_id = $id_transaksi";
$result_detail = $conn->query($sql_detail);
?>
<html>
<head>
    <title>Invoice</title>
</head>
<body>
    <h2>Invoice</h2>
    <p><strong>Nama:</strong> <?= $data_transaksi['nama']; ?></p>
    <p><strong>Alamat:</strong> <?= $data_transaksi['alamat']; ?></p>
    <p><strong>Telepon:</strong> <?= $data_transaksi['telepon']; ?></p>
    <p><strong>Tanggal:</strong> <?= $data_transaksi['tanggal']; ?></p>

    <table border="1">
        <tr>
            <th>Deskripsi</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Total</th>
        </tr>
        <?php while ($row = $result_detail->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['deskripsi']; ?></td>
            <td><?= $row['jumlah']; ?></td>
            <td><?= number_format($row['harga'], 2); ?></td>
            <td><?= number_format($row['jumlah'] * $row['harga'], 2); ?></td>
        </tr>
        <?php } ?>
    </table>
    <p><strong>Total Keseluruhan:</strong> Rp<?= number_format($data_transaksi['total'], 2); ?></p>
    <button onclick="window.print()">Cetak Invoice</button>
</body>
</html>
<?php
$conn->close();
?>