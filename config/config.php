<?php
$base_url_user = "http://localhost/fp-pemrograman-web/";
$base_url_admin = "http://localhost/fp-pemrograman-web/admin/";

$host = "localhost";
$user = "root";
$pass = "";
$db   = "vastacom_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();
?>