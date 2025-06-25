<?php
require '../../vendor/autoload.php';
use Dompdf\Dompdf;

include "../../config/config.php";

$id = $_GET['id'];
$query = "SELECT i.*, s.jenis_kerusakan, s.deskripsi, u.username AS pembuat, p.nama AS nama_pelanggan
          FROM invoice i
          JOIN service s ON i.service_id = s.id
          JOIN users u ON i.id_admin_pembuat = u.id
          JOIN pelanggan p ON s.id_pelanggan = p.id
          WHERE i.id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$html = '
<style>body{font-family:Arial} table{width:100%} th,td{padding:8px;}</style>
<h2>INVOICE</h2>
<p><strong>No Invoice:</strong> INV-'.$data['id'].'</p>
<p><strong>Nama Pelanggan:</strong> '.$data['nama_pelanggan'].'</p>
<p><strong>Jenis Kerusakan:</strong> '.$data['jenis_kerusakan'].'</p>
<p><strong>Deskripsi:</strong> '.$data['deskripsi'].'</p>
<hr>
<table border="1" cellpadding="5" cellspacing="0">
<tr><th>Item</th><th>Biaya</th></tr>
<tr><td>Perbaikan</td><td>Rp'.number_format($data['total_biaya'],0,',','.').'</td></tr>
</table>
<p><strong>Metode Pembayaran:</strong> '.$data['metode_pembayaran'].'</p>
<p><strong>Status Pembayaran:</strong> '.ucfirst($data['status_pembayaran']).'</p>
<p><strong>Tanggal:</strong> '.date('d-m-Y', strtotime($data['tanggal_invoice'])).'</p>
<p><strong>Dibuat oleh:</strong> '.$data['pembuat'].'</p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream("invoice-".$data['id'].".pdf", ["Attachment" => false]);
exit;
