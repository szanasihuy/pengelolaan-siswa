
<?php
require_once "../config/koneksi.php";

// Periksa ID ekstrakurikuler
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Hapus data ekstrakurikuler
$sql = "DELETE FROM ekstrakurikuler
        WHERE id_ekskul = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: index.php?pesan=hapus_berhasil");
    exit;
} else {
    echo "Gagal menghapus data ekstrakurikuler: "
        . htmlspecialchars(mysqli_error($conn));
}
?>
