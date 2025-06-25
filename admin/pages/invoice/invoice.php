<?php
session_start();
include "../../../config/config.php";


if (isset($_POST['submit_invoice'])) {
    $service_id = $_POST['service_id'];
    $total_biaya = $_POST['total_biaya'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $keterangan = $_POST['keterangan'] ?? '';
    $admin_id = $_SESSION['user_id'];

    $tanggal_invoice = date('Y-m-d H:i:s');
    $tanggal_pembayaran = ($status_pembayaran === 'lunas') ? $tanggal_invoice : null;

    $query = "INSERT INTO invoice (service_id, tanggal_invoice, total_biaya, keterangan, metode_pembayaran, status_pembayaran, tanggal_pembayaran, id_admin_pembuat, created_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isdssssi", $service_id, $tanggal_invoice, $total_biaya, $keterangan, $metode_pembayaran, $status_pembayaran, $tanggal_pembayaran, $admin_id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

if (isset($_POST['update_invoice'])) {
    $id = $_POST['id'];
    $total_biaya = $_POST['total_biaya'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $keterangan = $_POST['keterangan'] ?? '';
    $tanggal_pembayaran = ($status_pembayaran === 'lunas') ? date('Y-m-d H:i:s') : null;

    $query = "UPDATE invoice SET total_biaya=?, metode_pembayaran=?, keterangan=?, status_pembayaran=?, tanggal_pembayaran=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dssssi", $total_biaya, $metode_pembayaran, $keterangan, $status_pembayaran, $tanggal_pembayaran, $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

if (isset($_POST['hapus_id'])) {
    $id = $_POST['hapus_id'];
    $conn->query("DELETE FROM invoice WHERE id = $id");
    header("Location: index.php");
    exit;
}
