
<?php
session_start();

// Periksa jenis akun
$isAdmin = isset($_SESSION['id_admin']);

$isUser = (
    isset($_SESSION['id_user']) &&
    ($_SESSION['role'] ?? '') === 'user'
);

// Jika bukan admin maupun user, arahkan ke halaman login
if (!$isAdmin && !$isUser) {
    header("Location: ../login_user.php");
    exit;
}

require_once "../config/koneksi.php";

// Nama dashboard
$dashboard = $isAdmin ? "../index.php" : "../dashboard_user.php";

// Identitas akun
$namaUser = "Pengguna";
$fotoTampil = "";

// Ambil nama admin dari database
if ($isAdmin) {
    $idAdmin = (int) $_SESSION['id_admin'];

    $stmtAdmin = mysqli_prepare(
        $conn,
        "SELECT nama_admin FROM admin WHERE id_admin = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmtAdmin, "i", $idAdmin);
    mysqli_stmt_execute($stmtAdmin);

    $resultAdmin = mysqli_stmt_get_result($stmtAdmin);
    $adminData = mysqli_fetch_assoc($resultAdmin);

    if ($adminData) {
        $namaUser = $adminData["nama_admin"] ?? "Administrator";
    } else {
        $namaUser = "Administrator";
    }

    mysqli_stmt_close($stmtAdmin);
}

// Ambil identitas user dari database
if ($isUser) {
    $idUser = (int) $_SESSION['id_user'];

    $stmtUser = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE id_user = ? LIMIT 1"
    );

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

// Pencarian
$cari = isset($_GET['cari'])
    ? trim($_GET['cari'])
    : '';

$cari_sql = mysqli_real_escape_string($conn, $cari);

// Ambil data siswa
$sql = "SELECT * FROM siswa";

if ($cari !== '') {
    $sql .= " WHERE nis LIKE '%$cari_sql%'
              OR nama_siswa LIKE '%$cari_sql%'
              OR kelas LIKE '%$cari_sql%'
              OR jenis_kelamin LIKE '%$cari_sql%'
              OR alamat LIKE '%$cari_sql%'";
}

$sql .= " ORDER BY id_siswa DESC";

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
    <title>Data Siswa | ARSIP</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .btn-logout {
            display: inline-block;
            padding: 9px 16px;
            margin-left: 12px;
            background-color: #dc3545;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-logout:hover {
            background-color: #bb2d3b;
        }
    </style>
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
            <a href="index.php" class="active">Data Siswa</a>
            <a href="../ekstrakurikuler/index.php">Ekstrakurikuler</a>
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
                <p class="breadcrumb">Halaman / Data Siswa</p>
                <h1>Data Siswa</h1>
            </div>

            <!-- PROFIL DAN TOMBOL LOGOUT -->
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

                <a
                    href="../<?= $isAdmin ? 'logout_admin.php' : 'logout_user.php' ?>"
                    class="btn-logout"
                    onclick="return confirm('Yakin ingin logout?')"
                >
                    Logout
                </a>

            </div>

        </header>

        <section class="content-panel">

            <div class="table-header">
                <div>
                    <h2>Daftar Siswa</h2>
                    <p>Informasi data siswa sekolah.</p>
                </div>

                <?php if ($isAdmin): ?>
                    <a href="tambah.php" class="btn-primary">
                        + Tambah Siswa
                    </a>
                <?php endif; ?>
            </div>

            <!-- PENCARIAN -->

            <form method="GET" class="search-form">

                <input
                    type="text"
                    name="cari"
                    placeholder="Cari NIS, nama, kelas..."
                    value="<?= htmlspecialchars($cari) ?>"
                >

                <button type="submit">Cari</button>

                <a href="index.php">Reset</a>

            </form>

            <!-- TABEL -->

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Alamat</th>

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
                                    <?= htmlspecialchars($row['nis']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_siswa']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['kelas']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['jenis_kelamin']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['alamat'] ?? '-') ?>
                                </td>

                                <?php if ($isAdmin): ?>
                                    <td>
                                        <div class="action-buttons">

                                            <a
                                                href="edit.php?id=<?= (int)$row['id_siswa'] ?>"
                                                class="btn-edit"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="hapus.php?id=<?= (int)$row['id_siswa'] ?>"
                                                class="btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus siswa ini?')"
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
                            <td colspan="<?= $isAdmin ? 7 : 6 ?>" class="empty-data">

                                <?php if ($cari !== ''): ?>
                                    Data tidak ditemukan.
                                <?php else: ?>
                                    Belum ada data siswa.
                                <?php endif; ?>

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
