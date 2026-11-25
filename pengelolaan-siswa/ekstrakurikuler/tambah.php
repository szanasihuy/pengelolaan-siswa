
<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

$error = "";

$nama = "";
$pembina = "";
$keterangan = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST['nama_ekskul'] ?? '');
    $pembina = trim($_POST['pembina'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama === "" || $pembina === "") {
        $error = "Nama ekstrakurikuler dan pembina wajib diisi.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO ekstrakurikuler
            (nama_ekskul, pembina, keterangan)
            VALUES (?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $nama,
                $pembina,
                $keterangan
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: index.php");
                exit;
            }

            $error = "Gagal menyimpan data ekstrakurikuler.";
            mysqli_stmt_close($stmt);
        } else {
            $error = "Terjadi kesalahan saat memproses data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ekstrakurikuler | ARSIP</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<div class="layout">

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

    <main class="main-content">

        <header class="topbar">
            <div>
                <p class="breadcrumb">
                    Halaman / Ekstrakurikuler / Tambah
                </p>
                <h1>Tambah Ekstrakurikuler</h1>
            </div>

            <div class="admin-label">
                Administrator
            </div>
        </header>

        <section class="content-panel">

            <div class="table-header">
                <div>
                    <h2>Formulir Ekstrakurikuler</h2>
                    <p>Tambahkan informasi kegiatan ekstrakurikuler baru.</p>
                </div>
            </div>

            <?php if ($error !== ""): ?>
                <p class="empty-data">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST" class="form-data">

                <div class="form-group">
                    <label for="nama_ekskul">
                        Nama Ekstrakurikuler
                    </label>

                    <input
                        type="text"
                        id="nama_ekskul"
                        name="nama_ekskul"
                        placeholder="Masukkan nama ekstrakurikuler"
                        value="<?= htmlspecialchars($nama) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="pembina">
                        Nama Pembina
                    </label>

                    <input
                        type="text"
                        id="pembina"
                        name="pembina"
                        placeholder="Masukkan nama pembina"
                        value="<?= htmlspecialchars($pembina) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="keterangan">
                        Keterangan
                    </label>

                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="4"
                        placeholder="Tambahkan keterangan ekstrakurikuler"
                    ><?= htmlspecialchars($keterangan) ?></textarea>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-primary">
                        Simpan Data
                    </button>

                    <a href="index.php" class="btn-delete">
                        Kembali
                    </a>
                </div>

            </form>

        </section>

        <footer>
            Sistem Informasi Pengelolaan Data Siswa
            dan Nilai Ekstrakurikuler
        </footer>

    </main>

</div>

</body>
</html>
