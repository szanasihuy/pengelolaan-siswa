<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

$error = "";

$nis = "";
$nama_siswa = "";
$kelas = "";
$jenis_kelamin = "";
$alamat = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nis = trim($_POST["nis"] ?? "");
    $nama_siswa = trim($_POST["nama_siswa"] ?? "");
    $kelas = trim($_POST["kelas"] ?? "");
    $jenis_kelamin = trim($_POST["jenis_kelamin"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");

    if ($nis === "" || $nama_siswa === "" || $kelas === "" || $jenis_kelamin === "") {

        $error = "NIS, nama siswa, kelas, dan jenis kelamin wajib diisi.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO siswa (nis, nama_siswa, kelas, jenis_kelamin, alamat)
             VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {

            $error = "Query gagal: " . mysqli_error($conn);

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $nis,
                $nama_siswa,
                $kelas,
                $jenis_kelamin,
                $alamat
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: index.php");
                exit;

            } else {

                $error = "Data gagal disimpan: " . mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Siswa | ARSIP</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .student-form {
            margin-top: 25px;
        }

        .student-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 24px;
        }

        .student-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .student-field.full {
            grid-column: 1 / -1;
        }

        .student-field label {
            font-size: 14px;
            font-weight: 600;
            color: #26332b;
        }

        .student-field input,
        .student-field select,
        .student-field textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1px solid #b8bdb7;
            border-radius: 8px;
            background: white;
            color: #26332b;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        .student-field input,
        .student-field select {
            height: 44px;
        }

        .student-field textarea {
            min-height: 105px;
            resize: vertical;
        }

        .student-field input:focus,
        .student-field select:focus,
        .student-field textarea:focus {
            border-color: #6f8067;
            box-shadow: 0 0 0 3px rgba(111, 128, 103, 0.12);
        }

        .student-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e2dd;
        }

        .student-save,
        .student-cancel,
        .student-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 20px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            box-sizing: border-box;
        }

        .student-save {
            border: 1px solid #30352f;
            background: #dbe7d3;
            color: #26332b;
            box-shadow: 3px 3px 0 #30352f;
        }

        .student-cancel {
            border: 1px solid #30352f;
            background: #f5e4e1;
            color: #493b39;
        }

        .student-back {
            border: 1px solid #30352f;
            background: #dbe7d3;
            color: #26332b;
            box-shadow: 3px 3px 0 #30352f;
        }

        .student-error {
            margin: 20px 0;
            padding: 13px 15px;
            border: 1px solid #d6aaa5;
            border-radius: 8px;
            background: #f8e8e5;
            color: #8a3d35;
            font-size: 14px;
        }

        @media (max-width: 700px) {
            .student-form-grid {
                grid-template-columns: 1fr;
            }

            .student-field.full {
                grid-column: auto;
            }

            .student-actions {
                flex-direction: column;
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
            <a href="../index.php">Dashboard</a>

            <a href="index.php" class="active">Data Siswa</a>

            <a href="../ekstrakurikuler/index.php">
                Ekstrakurikuler
            </a>

            <a href="../nilai/index.php">
                Data Nilai
            </a>

            <a href="../laporan/index.php">
                Laporan
            </a>
        </nav>

        <div class="sidebar-bottom">
            <small>Pengelolaan Data Siswa</small>
            <small>Versi 1.0</small>
        </div>

    </aside>


    <!-- KONTEN -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="breadcrumb">
                    Halaman / Data Siswa / Tambah
                </p>

                <h1>Tambah Siswa</h1>
            </div>

            <div class="admin-label">
                Administrator
            </div>

        </header>


        <section class="content-panel">

            <div class="table-header">

                <div>
                    <h2>Form Data Siswa</h2>

                    <p>
                        Tambahkan data siswa baru.
                    </p>
                </div>

                <a href="index.php" class="student-back">
                    Kembali
                </a>

            </div>


            <?php if ($error !== ""): ?>

                <div class="student-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST" class="student-form">

                <div class="student-form-grid">

                    <div class="student-field">

                        <label for="nis">
                            NIS
                        </label>

                        <input
                            type="text"
                            id="nis"
                            name="nis"
                            placeholder="Masukkan NIS"
                            value="<?= htmlspecialchars($nis) ?>"
                            required
                        >

                    </div>


                    <div class="student-field">

                        <label for="nama_siswa">
                            Nama Siswa
                        </label>

                        <input
                            type="text"
                            id="nama_siswa"
                            name="nama_siswa"
                            placeholder="Masukkan nama siswa"
                            value="<?= htmlspecialchars($nama_siswa) ?>"
                            required
                        >

                    </div>


                    <div class="student-field">

                        <label for="kelas">
                            Kelas
                        </label>

                        <input
                            type="text"
                            id="kelas"
                            name="kelas"
                            placeholder="Contoh: XI PPLG 1"
                            value="<?= htmlspecialchars($kelas) ?>"
                            required
                        >

                    </div>


                    <div class="student-field">

                        <label for="jenis_kelamin">
                            Jenis Kelamin
                        </label>

                        <select
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option
                                value="Laki-laki"
                                <?= $jenis_kelamin === "Laki-laki" ? "selected" : "" ?>
                            >
                                Laki-laki
                            </option>

                            <option
                                value="Perempuan"
                                <?= $jenis_kelamin === "Perempuan" ? "selected" : "" ?>
                            >
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <div class="student-field full">

                        <label for="alamat">
                            Alamat
                        </label>

                        <textarea
                            id="alamat"
                            name="alamat"
                            placeholder="Masukkan alamat siswa"
                        ><?= htmlspecialchars($alamat) ?></textarea>

                    </div>

                </div>


                <div class="student-actions">

                    <button
                        type="submit"
                        class="student-save"
                    >
                        Simpan Siswa
                    </button>

                    <a
                        href="index.php"
                        class="student-cancel"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </section>


        <footer>
            Sistem Informasi Pengelolaan Data Siswa dan Nilai Ekstrakurikuler
        </footer>

    </main>

</div>

</body>
</html>