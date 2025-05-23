<?php
include "config/config.php";
include "inc/header.php";
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$file = "pages/" . $page . ".php";
if (file_exists($file)) {
    include $file;
} else {
    echo "<h1>404 - Halaman tidak ditemukan</h1>";
}
include "inc/footer.php";
?>