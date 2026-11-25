<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

require_once "config/koneksi.php";

/*
|--------------------------------------------------------------------------
| Fungsi menghitung jumlah data
|--------------------------------------------------------------------------
*/
function hitungData($conn, $tabel)
{
    $tabelValid = [
        'siswa',
        'ekstrakurikuler',
        'nilai_ekskul'
    ];

    if (!in_array($tabel, $tabelValid, true)) {
        return 0;
    }

    $result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM `$tabel`"
    );

    if (!$result) {
        return 0;
    }

    $data = mysqli_fetch_assoc($result);

    return (int) ($data['total'] ?? 0);
}


/*
|--------------------------------------------------------------------------
| Hitung data
|--------------------------------------------------------------------------
*/
$totalSiswa = hitungData($conn, 'siswa');
$totalEkskul = hitungData($conn, 'ekstrakurikuler');
$totalNilai = hitungData($conn, 'nilai_ekskul');


/*
|--------------------------------------------------------------------------
| Nama admin yang sedang login
|--------------------------------------------------------------------------
*/
$namaAdmin = htmlspecialchars(
    $_SESSION['nama_admin'] ?? 'Administrator',
    ENT_QUOTES,
    'UTF-8'
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Pengelolaan Siswa</title>

    <link rel="stylesheet" href="assets/style.css">

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

            <a href="index.php" class="active">
                Dashboard
            </a>

            <a href="siswa/index.php">
                Data Siswa
            </a>

            <a href="ekstrakurikuler/index.php">
                Ekstrakurikuler
            </a>

            <a href="nilai/index.php">
                Data Nilai
            </a>

            <a href="laporan/index.php">
                Laporan
            </a>

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
                    Halaman / Dashboard
                </p>

                <h1>Dashboard</h1>

            </div>


            <div class="admin-label">
                <?= $namaAdmin ?>
            </div>

        </header>


        <section class="intro">

            <div>

                <p class="eyebrow">
                    SISTEM INFORMASI SEKOLAH
                </p>

                <h2>
                    Pengelolaan Data Siswa
                </h2>

                <p>
                    Kelola informasi siswa, kegiatan ekstrakurikuler,
                    dan penilaian dalam satu sistem.
                </p>

            </div>

        </section>


        <section class="section-heading">

            <div>

                <h2>
                    Ringkasan Data
                </h2>

                <p>
                    Informasi jumlah data yang tersimpan.
                </p>

            </div>

        </section>


        <section class="stats">


            <div class="stat-card">

                <p>
                    Jumlah Siswa
                </p>

                <h3>
                    <?= $totalSiswa ?>
                </h3>

                <small>
                    Data siswa terdaftar
                </small>

            </div>


            <div class="stat-card">

                <p>
                    Ekstrakurikuler
                </p>

                <h3>
                    <?= $totalEkskul ?>
                </h3>

                <small>
                    Jumlah kegiatan
                </small>

            </div>


            <div class="stat-card">

                <p>
                    Data Penilaian
                </p>

                <h3>
                    <?= $totalNilai ?>
                </h3>

                <small>
                    Nilai yang telah dicatat
                </small>

            </div>


        </section>


        <section class="content-panel">

            <div>

                <h2>
                    Informasi Pengelolaan
                </h2>

                <p>
                    Pilih menu pada bagian navigasi untuk mulai
                    mengelola data.
                </p>

            </div>


            <div class="quick-links">

                <a href="siswa/index.php">
                    Kelola Data Siswa
                </a>

                <a href="ekstrakurikuler/index.php">
                    Kelola Ekstrakurikuler
                </a>

                <a href="nilai/index.php">
                    Kelola Nilai
                </a>

            </div>

        </section>


        <footer>

            Sistem Informasi Pengelolaan Data Siswa dan Nilai Ekstrakurikuler

        </footer>


    </main>

</div>


</body>

</html>