<?php
include "../config/config.php";
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teknisi')) {
    echo "Akses ditolak!";
    exit;
}
include "inc/header.php";
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$file = "pages/" . $page . ".php";
if (file_exists($file)) {
    include $file;
} else {
    echo "<h1>404 - Admin Page Not Found</h1>";
}
include "inc/footer.php";
?>