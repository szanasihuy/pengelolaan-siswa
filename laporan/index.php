
<?php
session_start();

/* ==============================
   CEK HAK AKSES ADMIN DAN USER
============================== */

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

/* ==============================
   PENGATURAN HALAMAN
============================== */

$dashboard = $isAdmin
    ? "../index.php"
    : "../dashboard_user.php";

/* ==============================
   AMBIL DATA LAPORAN
============================== */

$sql = "SELECT
            s.nis,
            s.nama_siswa,
            s.kelas,
            e.nama_ekskul,
            n.nilai,
            n.semester,
            n.tahun_ajaran
        FROM nilai_ekskul n
        LEFT JOIN siswa s
            ON n.id_siswa = s.id_siswa
        LEFT JOIN ekstrakurikuler e
            ON n.id_ekskul = e.id_ekskul
        ORDER BY s.nama_siswa ASC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Gagal mengambil laporan: " . mysqli_error($conn));
}

$total_data = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Data Nilai | ARSIP</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .report-info {
            display: flex;
            gap: 15px;
            margin: 20px 0 25px;
        }

        .report-card {
            background: var(--primary-light);
            border: 1.5px solid var(--border);
            border-radius: 6px;
            padding: 18px 24px;
            box-shadow: 3px 3px 0 var(--border);
            min-width: 180px;
        }

        .report-card small {
            display: block;
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 8px;
        }

        .report-card strong {
            font-family: 'Manrope', sans-serif;
            font-size: 25px;
        }

        .report-actions {
            display: flex;
            gap: 10px;
        }

        .report-title {
            display: none;
            text-align: center;
            margin-bottom: 25px;
        }

        .report-title h2 {
            font-size: 20px;
            color: black;
        }

        @media print {

            body {
                background: white !important;
            }

            .sidebar,
            .topbar,
            .no-print,
            footer {
                display: none !important;
            }

            .layout,
            .main-content {
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .content-panel {
                width: 100% !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            .report-title {
                display: block !important;
            }

            .report-info {
                margin-bottom: 20px;
            }

            .report-card {
                box-shadow: none;
            }

            .table-wrapper {
                overflow: visible !important;
            }

            .data-table {
                min-width: 100% !important;
                font-size: 10px !important;
            }

            .data-table th,
            .data-table td {
                border: 1px solid #333 !important;
                color: black !important;
                padding: 9px !important;
            }

            @page {
                size: landscape;
                margin: 15mm;
            }
        }
    </style>
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

            <a href="<?= htmlspecialchars($dashboard) ?>">
                Dashboard
            </a>

            <a href="../siswa/index.php">
                Data Siswa
            </a>

            <a href="../ekstrakurikuler/index.php">
                Ekstrakurikuler
            </a>

            <a href="../nilai/index.php">
                Data Nilai
            </a>

            <a href="index.php" class="active">
                Laporan
            </a>

        </nav>

        <div class="sidebar-bottom">
            <small>Pengelolaan Data Siswa</small>
            <small>Versi 1.0</small>
        </div>

    </aside>

    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="breadcrumb">Halaman / Laporan</p>
                <h1>Laporan Data Nilai</h1>
            </div>

        </header>

        <section class="content-panel">

            <!-- HEADER LAPORAN -->

            <div class="table-header">

                <div>
                    <h2>Rekap Nilai Ekstrakurikuler</h2>

                    <p>
                        Informasi hasil penilaian kegiatan
                        ekstrakurikuler seluruh siswa.
                    </p>
                </div>

                <div class="report-actions no-print">

                    <button
                        type="button"
                        class="btn-primary btn-print"
                        onclick="window.print()">

                        Cetak Laporan

                    </button>

                </div>

            </div>

            <!-- JUDUL CETAK -->

            <div class="report-title">

                <h2>LAPORAN DATA NILAI SISWA</h2>

                <p>
                    REKAP NILAI EKSTRAKURIKULER
                </p>

                <hr style="margin-top: 15px;">

            </div>

            <!-- INFORMASI DATA -->

            <div class="report-info">

                <div class="report-card">

                    <small>Total Data Nilai</small>

                    <strong>
                        <?= $total_data ?>
                    </strong>

                </div>

            </div>

            <!-- TABEL LAPORAN -->

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Ekstrakurikuler</th>
                            <th>Nilai</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($total_data > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($query)): ?>

                            <tr>

                                <td>
                                    <?= $no++ ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nis'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_siswa'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['kelas'] ?? '-') ?>
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

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8" class="empty-data">
                                Belum ada data nilai.
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
