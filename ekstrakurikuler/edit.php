
<?php
require_once "../config/koneksi.php";

// Periksa ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Ambil data ekstrakurikuler
$query = mysqli_query($conn, "SELECT * FROM ekstrakurikuler WHERE id_ekskul = $id");

if (!$query || mysqli_num_rows($query) == 0) {
    echo "Data ekstrakurikuler tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

// Proses update
if (isset($_POST['update'])) {

    $nama_ekskul = mysqli_real_escape_string($conn, $_POST['nama_ekskul']);
    $pembina = mysqli_real_escape_string($conn, $_POST['pembina']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $sql = "UPDATE ekstrakurikuler SET
                nama_ekskul = '$nama_ekskul',
                pembina = '$pembina',
                keterangan = '$keterangan'
            WHERE id_ekskul = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?pesan=edit_berhasil");
        exit;
    } else {
        $error = "Gagal memperbarui data ekstrakurikuler.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ekstrakurikuler</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <h2>ARSIP<span>.</span></h2>
            <small>SISTEM INFORMASI SEKOLAH</small>
        </div>

        <p class="menu-label">MENU</p>

        <nav>
            <a href="../index.php">Dashboard</a>
            <a href="../siswa/index.php">Data Siswa</a>
            <a href="index.php" class="active">Ekstrakurikuler</a>
            <a href="../nilai/index.php">Data Nilai</a>
            <a href="../laporan/index.php">Laporan</a>
        </nav>

        <div class="sidebar-bottom">
            <small>Pengelolaan Data Siswa</small>
            <small>Versi 1.0</small>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="topbar">
            <div>
                <p class="breadcrumb">Halaman / Ekstrakurikuler / Edit</p>
                <h1>Edit Ekstrakurikuler</h1>
            </div>

            <div class="admin-label">Administrator</div>
        </header>

        <!-- CONTENT -->
        <section class="content-panel">

            <div class="table-header">
                <div>
                    <h2>Form Edit Ekstrakurikuler</h2>
                    <p>Perbarui informasi kegiatan ekstrakurikuler.</p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" class="form-data">

                <div class="form-group">
                    <label>Nama Ekstrakurikuler</label>
                    <input
                        type="text"
                        name="nama_ekskul"
                        value="<?= htmlspecialchars($data['nama_ekskul']) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Nama Pembina</label>
                    <input
                        type="text"
                        name="pembina"
                        value="<?= htmlspecialchars($data['pembina'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
                </div>

                <div class="action-buttons">
                    <button type="submit" name="update" class="btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="index.php" class="btn-delete">
                        Kembali
                    </a>
                </div>

            </form>

        </section>

        <!-- FOOTER -->
        <footer>
            Sistem Informasi Pengelolaan Data Siswa dan Nilai Ekstrakurikuler
        </footer>

    </main>

</div>

</body>
</html>
