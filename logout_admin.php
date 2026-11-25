<?php
session_start();

// Pastikan yang logout adalah admin
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_user.php");
    exit;
}

// Hapus semua data session admin
$_SESSION = [];

// Hancurkan session
session_destroy();

// Arahkan ke halaman login
header("Location: login_user.php");
exit;
?>
