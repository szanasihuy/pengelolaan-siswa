
<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_siswa = (int)($_POST['id_siswa'] ?? 0);
    $id_ekskul = (int)($_POST['id_ekskul'] ?? 0);
    $nilai = (float)($_POST['nilai'] ?? 0);
    $semester = trim($_POST['semester'] ?? '');

    if (
        $id_siswa <= 0 ||
        $id_ekskul <= 0 ||
        $semester === '' ||
        $nilai < 0 ||
        $nilai > 100
    ) {
        $error = "Lengkapi semua data dengan benar.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO nilai_ekskul
            (id_siswa, id_ekskul, nilai, semester)
            VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iids",
            $id_siswa,
            $id_ekskul,
            $nilai,
            $semester
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php");
            exit;
        }

        $error = "Gagal menyimpan nilai: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}

$siswa = mysqli_query(
    $conn,
    "SELECT id_siswa, nis, nama_siswa
     FROM siswa
     ORDER BY nama_siswa"
);

$ekskul = mysqli_query(
    $conn,
    "SELECT id_ekskul, nama_ekskul
     FROM ekstrakurikuler
     ORDER BY nama_ekskul"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Nilai | ARSIP</title>
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
                <p class="breadcrumb">Halaman / Data Nilai / Tambah</p>
                <h1>Tambah Nilai</h1>
            </div>

            <div class="admin-label">Administrator</div>
        </header>

        <section class="content-panel">

            <div class="table-header">
                <div>
                    <h2>Form Tambah Nilai</h2>
                    <p>Masukkan data penilaian ekstrakurikuler siswa.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="empty-data">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-data">

                <div class="form-group">
                    <label for="id_siswa">Nama Siswa</label>
                    <select name="id_siswa" id="id_siswa" required>
                        <option value="">Pilih Siswa</option>

                        <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                            <option
                                value="<?= $row['id_siswa'] ?>"
                                <?= (($_POST['id_siswa'] ?? '') == $row['id_siswa']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nis'] . " - " . $row['nama_siswa']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_ekskul">Ekstrakurikuler</label>
                    <select name="id_ekskul" id="id_ekskul" required>
                        <option value="">Pilih Ekstrakurikuler</option>

                        <?php while ($row = mysqli_fetch_assoc($ekskul)): ?>
                            <option
                                value="<?= $row['id_ekskul'] ?>"
                                <?= (($_POST['id_ekskul'] ?? '') == $row['id_ekskul']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nama_ekskul']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nilai">Nilai</label>
                    <input
                        type="number"
                        id="nilai"
                        name="nilai"
                        min="0"
                        max="100"
                        step="0.01"
                        value="<?= htmlspecialchars($_POST['nilai'] ?? '') ?>"
                        placeholder="Masukkan nilai (0–100)"
                        required>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select name="semester" id="semester" required>
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil" <?= (($_POST['semester'] ?? '') === 'Ganjil') ? 'selected' : '' ?>>
                            Ganjil
                        </option>
                        <option value="Genap" <?= (($_POST['semester'] ?? '') === 'Genap') ? 'selected' : '' ?>>
                            Genap
                        </option>
                    </select>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-primary">
                        Simpan Nilai
                    </button>

                    <a href="index.php" class="btn-delete">
                        Kembali
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
