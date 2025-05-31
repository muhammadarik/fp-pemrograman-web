<?php
// pages/logout.php
// TIDAK PERLU session_start();
// TIDAK PERLU include '../config/config.php';
// TIDAK PERLU include '../functions/auth.php';

// Panggil fungsi logoutUser dari auth.php
logoutUser('/'); // Mengarahkan ke halaman home setelah logout
// exit(); // Fungsi logoutUser sudah mengandung exit()
?>