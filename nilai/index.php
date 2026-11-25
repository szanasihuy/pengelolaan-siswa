
<?php
session_start();

/* CEK HAK AKSES */
$isAdmin = isset($_SESSION['id_admin']);

$isUser = (
    isset($_SESSION['id_user']) &&
    ($_SESSION['role'] ?? '') === 'user'
);

if (!$isAdmin && !$isUser) {
    header("Location: ../login_user.php");
    exit;
}

require_once "../config/koneksi.php";

$dashboard = $isAdmin ? "../index.php" : "../dashboard_user.php";

/* AMBIL PROFIL ADMIN ATAU USER */
$namaUser = "Pengguna";
$fotoTampil = "";

if ($isAdmin) {
    $idAdmin = (int) $_SESSION['id_admin'];

    $stmtProfil = mysqli_prepare(
        $conn,
        "SELECT nama_admin FROM admin WHERE id_admin = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmtProfil, "i", $idAdmin);
    mysqli_stmt_execute($stmtProfil);

    $resultProfil = mysqli_stmt_get_result($stmtProfil);
    $dataProfil = mysqli_fetch_assoc($resultProfil);

    if ($dataProfil) {
        $namaUser = $dataProfil['nama_admin'] ?? 'Administrator';
    } else {
        $namaUser = 'Administrator';
    }

    mysqli_stmt_close($stmtProfil);

} elseif ($isUser) {
    $idUser = (int) $_SESSION['id_user'];

    $stmtProfil = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE id_user = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmtProfil, "i", $idUser);
    mysqli_stmt_execute($stmtProfil);

    $resultProfil = mysqli_stmt_get_result($stmtProfil);
    $dataProfil = mysqli_fetch_assoc($resultProfil);

    if ($dataProfil) {
        $namaUser = $dataProfil['nama']
            ?? $dataProfil['username']
            ?? 'Pengguna';

        $fotoTampil = $dataProfil['foto_profil'] ?? '';
    }

    mysqli_stmt_close($stmtProfil);
}

/* AMBIL DATA NILAI */
$sql = "SELECT
            n.*,
            s.nama_siswa,
            s.nis,
            e.nama_ekskul
        FROM nilai_ekskul n
        LEFT JOIN siswa s ON n.id_siswa = s.id_siswa
        LEFT JOIN ekstrakurikuler e ON n.id_ekskul = e.id_ekskul
        ORDER BY n.id_nilai DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Gagal mengambil data nilai: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nilai - ARSIP</title>
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
            <a href="<?= htmlspecialchars($dashboard) ?>">Dashboard</a>
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
                <p class="breadcrumb">Halaman / Data Nilai</p>
                <h1>Data Nilai</h1>
            </div>

            <!-- PROFIL ADMIN DAN USER -->
            <div class="user">

                <?php if ($isUser && !empty($fotoTampil)): ?>

                    <img
                        src="../<?= htmlspecialchars($fotoTampil) ?>"
                        alt="Foto profil"
                    >

                <?php else: ?>

                    <span class="user-avatar">
                        <?= htmlspecialchars(strtoupper(substr($namaUser, 0, 1))) ?>
                    </span>

                <?php endif; ?>

                <span><?= htmlspecialchars($namaUser) ?></span>

            </div>

        </header>

        <section class="content-panel">

            <div class="table-header">

                <div>
                    <h2>Daftar Nilai Ekstrakurikuler</h2>
                    <p>Informasi penilaian kegiatan siswa.</p>
                </div>

                <?php if ($isAdmin): ?>
                    <a href="tambah.php" class="btn-primary">
                        + Tambah Nilai
                    </a>
                <?php endif; ?>

            </div>

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Ekstrakurikuler</th>
                            <th>Nilai</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Keterangan</th>

                            <?php if ($isAdmin): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>

                    <?php $no = 1; ?>

                    <?php if (mysqli_num_rows($query) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($query)): ?>

                            <tr>

                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($row['nis'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_siswa'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_ekskul'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nilai'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['semester'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['tahun_ajaran'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['keterangan'] ?? '-') ?>
                                </td>

                                <?php if ($isAdmin): ?>

                                    <td>
                                        <div class="action-buttons">

                                            <a
                                                class="btn-edit"
                                                href="edit.php?id=<?= (int)$row['id_nilai'] ?>"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                class="btn-delete"
                                                href="hapus.php?id=<?= (int)$row['id_nilai'] ?>"
                                                onclick="return confirm('Yakin ingin menghapus nilai ini?')"
                                            >
                                                Hapus
                                            </a>

                                        </div>
                                    </td>

                                <?php endif; ?>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="<?= $isAdmin ? 9 : 8 ?>" class="empty-data">
                                Belum ada data nilai.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <footer>
            Sistem Informasi Pengelolaan Data Siswa dan Nilai Ekstrakurikuler
        </footer>

    </main>

</div>

</body>
</html>
