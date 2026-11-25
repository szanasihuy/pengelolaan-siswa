
<?php
session_start();
require_once "config/koneksi.php";

/* CEK LOGIN USER */
if (
    !isset($_SESSION["id_user"]) ||
    ($_SESSION["role"] ?? "") !== "user"
) {
    header("Location: login_user.php");
    exit;
}

$idUser = (int) $_SESSION["id_user"];
$pesanFoto = "";

/* AMBIL DATA USER */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM users WHERE id_user = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $idUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userData = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$userData) {
    session_destroy();
    header("Location: login_user.php");
    exit;
}

$namaUser = htmlspecialchars(
    $userData["nama"] ?? $userData["username"] ?? "Pengguna"
);

$fotoUser = $userData["foto_profil"] ?? "";

/* UPLOAD FOTO PROFIL */
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["foto_profil"])
) {
    $file = $_FILES["foto_profil"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        $pesanFoto = "Terjadi kesalahan saat mengunggah foto.";
    } elseif ($file["size"] > 2 * 1024 * 1024) {
        $pesanFoto = "Ukuran foto maksimal 2 MB.";
    } else {
        $jenisFile = mime_content_type($file["tmp_name"]);

        $formatDiizinkan = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        if (!isset($formatDiizinkan[$jenisFile])) {
            $pesanFoto = "Format foto harus JPG, PNG, atau WEBP.";
        } else {
            $folderAssets = __DIR__ . DIRECTORY_SEPARATOR . "assets";
            $folderFoto = $folderAssets . DIRECTORY_SEPARATOR . "profil";

            if (!is_dir($folderAssets)) {
                $pesanFoto = "Folder assets tidak ditemukan.";
            } else {
                /*
                 * CEK FOLDER PROFIL
                 * mkdir hanya dijalankan jika folder benar-benar belum ada.
                 */
                clearstatcache(true, $folderFoto);

                if (!is_dir($folderFoto)) {
                    @mkdir($folderFoto, 0755);
                }

                clearstatcache(true, $folderFoto);

                if (!is_dir($folderFoto)) {
                    $pesanFoto = "Folder profil tidak tersedia. Periksa folder assets.";
                } else {
                    $ekstensi = $formatDiizinkan[$jenisFile];

                    $namaFile = "user_" . $idUser . "_"
                        . bin2hex(random_bytes(8)) . "." . $ekstensi;

                    $lokasiFoto = $folderFoto . DIRECTORY_SEPARATOR . $namaFile;
                    $pathDatabase = "assets/profil/" . $namaFile;

                    if (move_uploaded_file($file["tmp_name"], $lokasiFoto)) {
                        $update = mysqli_prepare(
                            $conn,
                            "UPDATE users SET foto_profil = ? WHERE id_user = ?"
                        );

                        mysqli_stmt_bind_param(
                            $update,
                            "si",
                            $pathDatabase,
                            $idUser
                        );

                        if (mysqli_stmt_execute($update)) {
                            mysqli_stmt_close($update);

                            if (
                                $fotoUser &&
                                strpos($fotoUser, "assets/profil/") === 0
                            ) {
                                $fotoLama = __DIR__ . DIRECTORY_SEPARATOR
                                    . str_replace("/", DIRECTORY_SEPARATOR, $fotoUser);

                                if (is_file($fotoLama)) {
                                    unlink($fotoLama);
                                }
                            }

                            header("Location: dashboard_user.php?foto=berhasil");
                            exit;
                        } else {
                            unlink($lokasiFoto);
                            $pesanFoto = "Gagal menyimpan foto ke database.";
                            mysqli_stmt_close($update);
                        }
                    } else {
                        $pesanFoto = "Gagal mengunggah foto. Periksa izin folder profil.";
                    }
                }
            }
        }
    }
}

if (isset($_GET["foto"]) && $_GET["foto"] === "berhasil") {
    $pesanFoto = "Foto profil berhasil diperbarui.";
}

/* FOTO PROFIL */
$fotoTampil = $fotoUser ?: "";
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Pengguna | ARSIP</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'DM Sans', sans-serif;
    background: #f7f3eb;
    color: #343a32;
}

.layout {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    width: 250px;
    background: #dce6d3;
    border-right: 2px solid #373b33;
    padding: 30px 20px;
    flex-shrink: 0;
}

.brand {
    font: 800 29px 'Manrope', sans-serif;
    margin-bottom: 5px;
}

.brand span {
    color: #819777;
}

.subtitle {
    font-size: 10px;
    letter-spacing: 2px;
    margin-bottom: 40px;
}

.menu-title {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    color: #819777;
    margin: 25px 0 12px;
}

.menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 12px;
    margin-bottom: 7px;
    text-decoration: none;
    color: #343a32;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    transition: .2s;
}

.menu a:hover,
.menu a.active {
    background: #fffdf8;
    border: 1px solid #373b33;
    box-shadow: 3px 3px 0 #373b33;
}

.menu i {
    font-size: 17px;
}

.logout {
    display: block;
    margin-top: 45px;
    padding: 13px;
    text-align: center;
    background: #edc8cb;
    color: #343a32;
    text-decoration: none;
    font-size: 12px;
    font-weight: 800;
    border: 1.5px solid #373b33;
    box-shadow: 3px 3px 0 #373b33;
}

/* MAIN */
.main {
    flex: 1;
    min-width: 0;
    padding: 35px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 35px;
}

.topbar small {
    font-size: 10px;
    letter-spacing: 2px;
    font-weight: 800;
    color: #819777;
}

.user {
    background: #fffdf8;
    border: 1.5px solid #373b33;
    padding: 10px 16px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 9px;
}

.user img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #373b33;
}

/* WELCOME */
.welcome {
    background: #fffdf8;
    border: 2px solid #373b33;
    box-shadow: 5px 5px 0 #373b33;
    padding: 35px;
    position: relative;
    overflow: hidden;
}

.welcome small {
    color: #819777;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
}

.welcome h1 {
    font: 800 34px 'Manrope', sans-serif;
    margin: 13px 0;
}

.welcome p {
    font-size: 13px;
    color: #77786e;
    line-height: 1.8;
}

.welcome::after {
    content: "✿";
    position: absolute;
    right: 35px;
    top: 15px;
    font-size: 90px;
    color: #edc8cb;
}

/* SECTION */
.section-title {
    margin: 40px 0 18px;
}

.section-title h2 {
    font: 800 21px 'Manrope', sans-serif;
}

.section-title p {
    font-size: 12px;
    color: #88897e;
    margin-top: 5px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.card {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 23px;
    text-decoration: none;
    color: #343a32;
    border: 1.5px solid #373b33;
    box-shadow: 4px 4px 0 #373b33;
    transition: .2s;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 6px 7px 0 #373b33;
}

.card:nth-child(1) {
    background: #dce6d3;
}

.card:nth-child(2) {
    background: #f8e5e3;
}

.card:nth-child(3) {
    background: #f2dfa4;
}

.card:nth-child(4) {
    background: #fffdf8;
}

.card-icon {
    width: 52px;
    height: 52px;
    display: grid;
    place-items: center;
    background: rgba(255,255,255,.65);
    border: 1px solid #373b33;
    font-size: 23px;
    flex-shrink: 0;
}

.card h3 {
    font: 800 15px 'Manrope', sans-serif;
    margin-bottom: 5px;
}

.card p {
    font-size: 11px;
    color: #666b60;
    line-height: 1.6;
}

.card .arrow {
    margin-left: auto;
    font-size: 20px;
}

.notice {
    margin-top: 30px;
    background: #f8e5e3;
    border: 1.5px solid #373b33;
    padding: 20px;
    font-size: 12px;
    line-height: 1.8;
}

.footer {
    margin-top: 35px;
    text-align: center;
    color: #929083;
    font-size: 10px;
    letter-spacing: 1px;
}

/* PROFIL */
.profile-section {
    margin-top: 35px;
    background: #fffdf8;
    border: 1.5px solid #373b33;
    box-shadow: 4px 4px 0 #373b33;
    padding: 25px;
}

.profile-section h2 {
    font: 800 20px 'Manrope', sans-serif;
    margin-bottom: 18px;
}

.profile-content {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.profile-photo {
    width: 95px;
    height: 95px;
    border-radius: 50%;
    border: 2px solid #373b33;
    object-fit: cover;
    background: #dce6d3;
}

.profile-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.profile-form input {
    max-width: 100%;
    font-family: inherit;
    font-size: 12px;
}

.profile-form button {
    padding: 11px 15px;
    background: #dce6d3;
    border: 1.5px solid #373b33;
    box-shadow: 3px 3px 0 #373b33;
    font-weight: 800;
    cursor: pointer;
}

.profile-message {
    font-size: 12px;
    margin-bottom: 15px;
    color: #819777;
    font-weight: 700;
}

/* RESPONSIVE */
@media(max-width: 750px) {
    .layout {
        display: block;
    }

    .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 2px solid #373b33;
        padding: 20px;
    }

    .subtitle {
        margin-bottom: 15px;
    }

    .menu {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 5px;
    }

    .menu-title {
        margin-top: 15px;
    }

    .logout {
        margin-top: 15px;
    }

    .main {
        padding: 22px 16px;
    }

    .welcome {
        padding: 25px;
    }

    .welcome h1 {
        font-size: 26px;
    }

    .welcome::after {
        font-size: 55px;
        right: 15px;
    }
}

@media(max-width: 450px) {
    .cards {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <div class="brand">ARSIP<span>.</span></div>

        <div class="subtitle">
            SISTEM INFORMASI SEKOLAH
        </div>

        <div class="menu-title">MENU UTAMA</div>

        <nav class="menu">

            <a href="dashboard_user.php" class="active">
                <i class="bi bi-grid"></i> Dashboard
            </a>

            <a href="siswa/index.php">
                <i class="bi bi-people"></i> Data Siswa
            </a>

            <a href="ekstrakurikuler/index.php">
                <i class="bi bi-journal-bookmark"></i> Ekstrakurikuler
            </a>

            <a href="nilai/index.php">
                <i class="bi bi-clipboard-data"></i> Data Nilai
            </a>

            <a href="laporan/index.php">
                <i class="bi bi-file-earmark-text"></i> Laporan
            </a>

        </nav>

        <a href="logout_user.php"
           class="logout"
           onclick="return confirm('Yakin ingin keluar?')">

            <i class="bi bi-box-arrow-right"></i> KELUAR

        </a>

    </aside>

    <main class="main">

        <div class="topbar">

            <small>ARSIP / USER DASHBOARD</small>

            <div class="user">

                <?php if ($fotoTampil): ?>
                    <img src="<?= htmlspecialchars($fotoTampil) ?>"
                         alt="Foto profil">
                <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                <?php endif; ?>

                <?= $namaUser ?>

            </div>

        </div>

        <section class="welcome">

            <small>WELCOME TO ARSIP</small>

            <h1>Halo, <?= $namaUser ?>!</h1>

            <p>
                Selamat datang di sistem informasi pengelolaan siswa.
                Kamu dapat melihat informasi siswa, ekstrakurikuler,
                nilai, dan laporan melalui menu yang tersedia.
            </p>

        </section>

        <section class="section-title">
            <h2>Jelajahi Menu</h2>
            <p>Pilih informasi yang ingin kamu lihat.</p>
        </section>

        <section class="cards">

            <a href="siswa/index.php" class="card">
                <div class="card-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h3>Data Siswa</h3>
                    <p>Lihat informasi dan daftar siswa.</p>
                </div>
                <i class="bi bi-arrow-up-right arrow"></i>
            </a>

            <a href="ekstrakurikuler/index.php" class="card">
                <div class="card-icon">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
                <div>
                    <h3>Ekstrakurikuler</h3>
                    <p>Lihat daftar kegiatan ekstrakurikuler.</p>
                </div>
                <i class="bi bi-arrow-up-right arrow"></i>
            </a>

            <a href="nilai/index.php" class="card">
                <div class="card-icon">
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div>
                    <h3>Data Nilai</h3>
                    <p>Lihat informasi nilai ekstrakurikuler.</p>
                </div>
                <i class="bi bi-arrow-up-right arrow"></i>
            </a>

            <a href="laporan/index.php" class="card">
                <div class="card-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h3>Laporan</h3>
                    <p>Lihat dan cetak laporan pengelolaan siswa.</p>
                </div>
                <i class="bi bi-arrow-up-right arrow"></i>
            </a>

        </section>

        <!-- PROFIL USER -->
        <section class="profile-section">

            <h2>
                <i class="bi bi-person-circle"></i>
                Profil Saya
            </h2>

            <?php if ($pesanFoto !== ""): ?>
                <div class="profile-message">
                    <?= htmlspecialchars($pesanFoto) ?>
                </div>
            <?php endif; ?>

            <div class="profile-content">

                <?php if ($fotoTampil): ?>
                    <img
                        src="<?= htmlspecialchars($fotoTampil) ?>"
                        class="profile-photo"
                        alt="Foto profil"
                    >
                <?php else: ?>
                    <div class="profile-photo"
                         style="display:grid;place-items:center;font-size:40px;">
                        <i class="bi bi-person"></i>
                    </div>
                <?php endif; ?>

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="profile-form"
                >

                    <label for="foto_profil">
                        Pilih foto profil (JPG, PNG, WEBP)
                    </label>

                    <input
                        type="file"
                        name="foto_profil"
                        id="foto_profil"
                        accept=".jpg,.jpeg,.png,.webp"
                        required
                    >

                    <button type="submit">
                        <i class="bi bi-camera"></i>
                        SIMPAN FOTO PROFIL
                    </button>

                </form>

            </div>

        </section>

        <div class="notice">

            <strong>
                <i class="bi bi-info-circle"></i>
                Informasi Akses
            </strong>

            <br>

            Akun pengguna hanya memiliki akses untuk melihat informasi.
            Pengelolaan, penambahan, perubahan, dan penghapusan data
            tetap menjadi hak administrator.

        </div>

        <div class="footer">
            ARSIP — STUDENT MANAGEMENT SYSTEM © 2026
        </div>

    </main>

</div>

</body>
</html>
