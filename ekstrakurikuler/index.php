
<?php
session_start();

/* CEK LOGIN ADMIN DAN USER */
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

/* PENGATURAN DASHBOARD */
$dashboard = $isAdmin
    ? "../index.php"
    : "../dashboard_user.php";

/* AMBIL IDENTITAS ADMIN DAN USER */
$namaUser = "Pengguna";
$fotoTampil = "";

if ($isAdmin) {
    $idAdmin = (int) $_SESSION['id_admin'];

    $stmtProfil = mysqli_prepare(
        $conn,
        "SELECT nama_admin FROM admin WHERE id_admin = ? LIMIT 1"
    );

    if ($stmtProfil) {
        mysqli_stmt_bind_param($stmtProfil, "i", $idAdmin);
        mysqli_stmt_execute($stmtProfil);

        $resultProfil = mysqli_stmt_get_result($stmtProfil);
        $dataProfil = mysqli_fetch_assoc($resultProfil);

        if ($dataProfil) {
            $namaUser = $dataProfil['nama_admin'];
        }

        mysqli_stmt_close($stmtProfil);
    }

} elseif ($isUser) {
    $idUser = (int) $_SESSION['id_user'];

    $stmtUser = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE id_user = ? LIMIT 1"
    );

    if ($stmtUser) {
        mysqli_stmt_bind_param($stmtUser, "i", $idUser);
        mysqli_stmt_execute($stmtUser);

        $resultUser = mysqli_stmt_get_result($stmtUser);
        $userData = mysqli_fetch_assoc($resultUser);

        if ($userData) {
            $namaUser = $userData["nama"]
                ?? $userData["username"]
                ?? "Pengguna";

            $fotoTampil = $userData["foto_profil"] ?? "";
        }

        mysqli_stmt_close($stmtUser);
    }
}

/* PENCARIAN */
$cari = isset($_GET['cari'])
    ? trim($_GET['cari'])
    : '';

$cari_sql = mysqli_real_escape_string($conn, $cari);

/* AMBIL DATA EKSTRAKURIKULER */
$sql = "SELECT * FROM ekstrakurikuler";

if ($cari !== '') {
    $sql .= " WHERE nama_ekskul LIKE '%$cari_sql%'
              OR pembina LIKE '%$cari_sql%'
              OR keterangan LIKE '%$cari_sql%'";
}

$sql .= " ORDER BY id_ekskul DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$total_data = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Ekstrakurikuler | ARSIP</title>
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
                <p class="breadcrumb">Halaman / Ekstrakurikuler</p>
                <h1>Ekstrakurikuler</h1>
            </div>

            <!-- PROFIL ADMIN DAN USER -->
            <div class="user">

                <?php if (!empty($fotoTampil)): ?>

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
                    <h2>Daftar Ekstrakurikuler</h2>
                    <p>Informasi kegiatan ekstrakurikuler sekolah.</p>
                </div>

                <?php if ($isAdmin): ?>
                    <a href="tambah.php" class="btn-primary">
                        + Tambah Ekstrakurikuler
                    </a>
                <?php endif; ?>

            </div>

            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari nama ekskul atau pembina..."
                    value="<?= htmlspecialchars($cari) ?>"
                >

                <button type="submit">Cari</button>
                <a href="index.php">Reset</a>

            </form>

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Ekstrakurikuler</th>
                            <th>Pembina</th>
                            <th>Keterangan</th>

                            <?php if ($isAdmin): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($total_data > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($query)): ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($row['nama_ekskul']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['pembina'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['keterangan'] ?? '-') ?>
                                </td>

                                <?php if ($isAdmin): ?>
                                    <td>
                                        <div class="action-buttons">

                                            <a
                                                href="edit.php?id=<?= (int)$row['id_ekskul'] ?>"
                                                class="btn-edit"
                                            >Edit</a>

                                            <a
                                                href="hapus.php?id=<?= (int)$row['id_ekskul'] ?>"
                                                class="btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus ekstrakurikuler ini?')"
                                            >Hapus</a>

                                        </div>
                                    </td>
                                <?php endif; ?>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="<?= $isAdmin ? 5 : 4 ?>" class="empty-data">
                                <?= $cari !== ''
                                    ? 'Data tidak ditemukan.'
                                    : 'Belum ada data ekstrakurikuler.' ?>
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <footer>
            Sistem Informasi Pengelolaan Data Siswa
            dan Nilai Ekstrakurikuler
        </footer>

    </main>

</div>

</body>
</html>
