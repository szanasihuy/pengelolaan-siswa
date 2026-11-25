<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

/* AMBIL ID NILAI */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

/* AMBIL DATA NILAI */
$sql_data = "SELECT * FROM nilai_ekskul WHERE id_nilai = $id";
$query_data = mysqli_query($conn, $sql_data);

if (!$query_data || mysqli_num_rows($query_data) == 0) {
    die("Data nilai tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query_data);

/* AMBIL DATA SISWA */
$query_siswa = mysqli_query(
    $conn,
    "SELECT id_siswa, nis, nama_siswa FROM siswa ORDER BY nama_siswa ASC"
);

if (!$query_siswa) {
    die("Gagal mengambil data siswa: " . mysqli_error($conn));
}

/* AMBIL DATA EKSTRAKURIKULER */
$query_ekskul = mysqli_query(
    $conn,
    "SELECT id_ekskul, nama_ekskul FROM ekstrakurikuler ORDER BY nama_ekskul ASC"
);

if (!$query_ekskul) {
    die("Gagal mengambil data ekstrakurikuler: " . mysqli_error($conn));
}

/* PROSES UPDATE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_siswa = (int)($_POST['id_siswa'] ?? 0);
    $id_ekskul = (int)($_POST['id_ekskul'] ?? 0);
    $nilai = trim($_POST['nilai'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    $tahun_ajaran = trim($_POST['tahun_ajaran'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    /* VALIDASI */
    if (
        $id_siswa <= 0 ||
        $id_ekskul <= 0 ||
        $nilai === '' ||
        $semester === '' ||
        $tahun_ajaran === ''
    ) {
        $error = "Semua data wajib diisi.";
    } elseif (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $error = "Nilai harus berada antara 0 sampai 100.";
    } else {

        $nilai = (float)$nilai;

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE nilai_ekskul
             SET id_siswa = ?,
                 id_ekskul = ?,
                 nilai = ?,
                 semester = ?,
                 tahun_ajaran = ?,
                 keterangan = ?
             WHERE id_nilai = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iidsssi",
            $id_siswa,
            $id_ekskul,
            $nilai,
            $semester,
            $tahun_ajaran,
            $keterangan,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menyimpan perubahan: " . mysqli_stmt_error($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Nilai | ARSIP</title>
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
            <a href="../ekstrakurikuler/index.php">Ekstrakurikuler</a>
            <a href="index.php" class="active">Data Nilai</a>
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
                <p class="breadcrumb">Halaman / Data Nilai / Edit</p>
                <h1>Edit Nilai</h1>
            </div>

            <div class="admin-label">Administrator</div>
        </header>

        <section class="content-panel">

            <div>
                <h2>Edit Data Nilai Ekstrakurikuler</h2>
                <p>Ubah informasi penilaian siswa di bawah ini.</p>
            </div>

            <?php if (isset($error)): ?>
                <p style="color: red; margin-top: 15px;">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <form method="POST" class="form-data">

                <!-- SISWA -->
                <div class="form-group">
                    <label for="id_siswa">Nama Siswa</label>

                    <select name="id_siswa" id="id_siswa" required>
                        <option value="">-- Pilih Siswa --</option>

                        <?php while ($siswa = mysqli_fetch_assoc($query_siswa)): ?>
                            <option
                                value="<?= (int)$siswa['id_siswa'] ?>"
                                <?= ((int)$siswa['id_siswa'] === (int)$data['id_siswa']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($siswa['nis']) ?> -
                                <?= htmlspecialchars($siswa['nama_siswa']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <!-- EKSTRAKURIKULER -->
                <div class="form-group">
                    <label for="id_ekskul">Ekstrakurikuler</label>

                    <select name="id_ekskul" id="id_ekskul" required>
                        <option value="">-- Pilih Ekstrakurikuler --</option>

                        <?php while ($ekskul = mysqli_fetch_assoc($query_ekskul)): ?>
                            <option
                                value="<?= (int)$ekskul['id_ekskul'] ?>"
                                <?= ((int)$ekskul['id_ekskul'] === (int)$data['id_ekskul']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($ekskul['nama_ekskul']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <!-- NILAI DAN SEMESTER -->
                <div class="form-group">
                    <label for="nilai">Nilai</label>

                    <input
                        type="number"
                        id="nilai"
                        name="nilai"
                        min="0"
                        max="100"
                        step="0.01"
                        value="<?= htmlspecialchars((string)($data['nilai'] ?? '')) ?>"
                        required
                    >

                    <small style="color: var(--muted);">
                        Masukkan nilai antara 0–100.
                    </small>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>

                    <select name="semester" id="semester" required>
                        <option value="">-- Pilih Semester --</option>

                        <option
                            value="Ganjil"
                            <?= (($data['semester'] ?? '') === 'Ganjil') ? 'selected' : '' ?>
                        >
                            Ganjil
                        </option>

                        <option
                            value="Genap"
                            <?= (($data['semester'] ?? '') === 'Genap') ? 'selected' : '' ?>
                        >
                            Genap
                        </option>
                    </select>
                </div>

                <!-- TAHUN AJARAN -->
                <div class="form-group">
                    <label for="tahun_ajaran">Tahun Ajaran</label>

                    <input
                        type="text"
                        id="tahun_ajaran"
                        name="tahun_ajaran"
                        value="<?= htmlspecialchars($data['tahun_ajaran'] ?? '') ?>"
                        placeholder="Contoh: 2026/2027"
                        required
                    >
                </div>

                <!-- KETERANGAN -->
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>

                    <textarea
                        id="keterangan"
                        name="keterangan"
                        placeholder="Tambahkan keterangan jika diperlukan..."
                    ><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
                </div>

                <!-- TOMBOL -->
                <div class="action-buttons">

                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
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