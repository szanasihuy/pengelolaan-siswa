
<?php
require_once "../config/koneksi.php";

// Periksa ID siswa
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Hapus data siswa
$sql = "DELETE FROM siswa WHERE id_siswa = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: index.php?pesan=hapus_berhasil");
    exit;
} else {
    echo "Gagal menghapus data siswa: " . htmlspecialchars(mysqli_error($conn));
}
?>
