<?php
session_start();
require_once "config/koneksi.php";

if (isset($_SESSION['id_admin'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST["nama"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $konfirmasi = $_POST["konfirmasi"] ?? "";

    if ($nama === "" || $username === "" || $password === "" || $konfirmasi === "") {

        $error = "Semua data harus diisi.";

    } elseif (strlen($username) < 3) {

        $error = "Username minimal 3 karakter.";

    } elseif (strlen($password) < 5) {

        $error = "Password minimal 5 karakter.";

    } elseif ($password !== $konfirmasi) {

        $error = "Konfirmasi password tidak sama.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Pastikan tabel admins tersedia
        |--------------------------------------------------------------------------
        */

        $buatTabel = "
        CREATE TABLE IF NOT EXISTS admins (
            id_admin INT AUTO_INCREMENT PRIMARY KEY,
            nama_admin VARCHAR(100) NOT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";

        if (!mysqli_query($conn, $buatTabel)) {

            $error = "Gagal menyiapkan database.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Cek username
            |--------------------------------------------------------------------------
            */

            $cek = mysqli_prepare(
                $conn,
                "SELECT id_admin FROM admins WHERE username = ? LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $cek,
                "s",
                $username
            );

            mysqli_stmt_execute($cek);

            $hasil = mysqli_stmt_get_result($cek);

            if (mysqli_num_rows($hasil) > 0) {

                $error = "Username sudah digunakan.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Simpan password dengan aman
                |--------------------------------------------------------------------------
                */

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO admins
                    (nama_admin, username, password)
                    VALUES (?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sss",
                    $nama,
                    $username,
                    $passwordHash
                );

                if (mysqli_stmt_execute($stmt)) {

                    mysqli_stmt_close($stmt);
                    mysqli_stmt_close($cek);

                    header("Location: login.php?daftar=berhasil");
                    exit;

                } else {

                    $error = "Gagal membuat akun.";
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_stmt_close($cek);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register | ARSIP.</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

:root {
    --background: #f7f3eb;
    --surface: #fffdf8;
    --sage: #dce6d3;
    --sage-dark: #819777;
    --pink: #edc8cb;
    --pink-light: #f8e5e3;
    --yellow: #f2dfa4;
    --text: #343a32;
    --muted: #88897e;
    --border: #373b33;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 35px 20px;
    background: var(--background);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
}

.register-container {
    width: 100%;
    max-width: 1000px;
    min-height: 590px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--surface);
    border: 2px solid var(--border);
    box-shadow: 8px 8px 0 var(--border);
    animation: appear .5s ease;
}

@keyframes appear {

    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* LEFT */

.intro-panel {
    position: relative;
    overflow: hidden;
    background: var(--sage);
    padding: 38px;
    border-right: 2px solid var(--border);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.brand {
    font-family: 'Manrope', sans-serif;
    font-size: 29px;
    font-weight: 800;
    letter-spacing: -1.5px;
}

.brand span {
    color: var(--sage-dark);
}

.brand-subtitle {
    font-size: 9px;
    letter-spacing: 2px;
    margin-top: 7px;
    color: #707969;
    font-weight: 700;
}

.intro-content {
    position: relative;
    z-index: 1;
    margin: 55px 0;
}

.eyebrow {
    display: inline-block;
    background: var(--surface);
    border: 1.5px solid var(--border);
    padding: 9px 11px;
    font-size: 9px;
    letter-spacing: 1.5px;
    font-weight: 800;
    margin-bottom: 24px;
    box-shadow: 2px 2px 0 var(--border);
}

.intro-content h1 {
    font-family: 'Manrope', sans-serif;
    font-size: clamp(48px, 6vw, 70px);
    line-height: .96;
    letter-spacing: -5px;
    font-weight: 800;
}

.intro-content h1 span {
    color: var(--sage-dark);
}

.intro-content p {
    max-width: 280px;
    font-size: 13px;
    line-height: 1.8;
    color: #60685a;
    margin-top: 23px;
}

.intro-tag {
    display: inline-block;
    width: fit-content;
    padding: 12px 15px;
    border: 1.5px solid var(--border);
    background: var(--pink);
    box-shadow: 3px 3px 0 var(--border);
    font-size: 10px;
    font-weight: 800;
    margin-top: 27px;
}

.deco {
    position: absolute;
    right: 30px;
    bottom: 95px;
    width: 85px;
    height: 85px;
    background: var(--yellow);
    border: 2px solid var(--border);
    box-shadow: 4px 4px 0 var(--border);
    transform: rotate(12deg);
}

.deco::before,
.deco::after {
    content: "";
    position: absolute;
    background: var(--border);
}

.deco::before {
    width: 2px;
    height: 100%;
    left: 50%;
}

.deco::after {
    width: 100%;
    height: 2px;
    top: 50%;
}

.intro-footer {
    font-size: 9px;
    letter-spacing: 1.5px;
    line-height: 1.9;
    color: #697263;
    font-weight: 700;
}


/* RIGHT */

.form-panel {
    padding: 45px 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.form-label {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    color: var(--sage-dark);
    margin-bottom: 17px;
}

.form-panel h2 {
    font-family: 'Manrope', sans-serif;
    font-size: 39px;
    font-weight: 800;
    letter-spacing: -2px;
    margin-bottom: 10px;
}

.form-description {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.8;
    margin-bottom: 27px;
}

.form-group {
    margin-bottom: 17px;
}

.form-group label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
    margin-bottom: 9px;
}

.form-group input {
    width: 100%;
    height: 45px;
    border: 1.5px solid var(--border);
    background: #faf9f4;
    padding: 0 14px;
    font-family: inherit;
    font-size: 13px;
    color: var(--text);
    outline: none;
    transition: .2s ease;
}

.form-group input:focus {
    background: white;
    box-shadow: 3px 3px 0 var(--sage-dark);
}

.form-group input::placeholder {
    color: #aaa99e;
}

.password-box {
    display: flex;
    gap: 7px;
}

.password-box input {
    flex: 1;
    min-width: 0;
}

.toggle-password {
    padding: 0 12px;
    border: 1.5px solid var(--border);
    background: var(--yellow);
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
}

.register-button {
    width: 100%;
    padding: 15px;
    margin-top: 5px;
    background: var(--sage);
    color: var(--text);
    border: 1.5px solid var(--border);
    box-shadow: 4px 4px 0 var(--border);
    font-family: inherit;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
    transition: .2s ease;
}

.register-button:hover {
    background: var(--pink);
    transform: translate(-2px, -2px);
    box-shadow: 6px 6px 0 var(--border);
}

.error-message {
    padding: 12px;
    background: var(--pink-light);
    border: 1.5px solid var(--border);
    box-shadow: 3px 3px 0 var(--border);
    font-size: 12px;
    color: #854b43;
    font-weight: 600;
    margin-bottom: 17px;
}

.login-link {
    text-align: center;
    margin-top: 20px;
}

.login-link a {
    color: var(--sage-dark);
    font-size: 11px;
    font-weight: 800;
    text-decoration: underline;
    text-underline-offset: 3px;
}

.form-footer {
    border-top: 1.5px solid #dedbd1;
    padding-top: 15px;
    margin-top: 25px;
    color: #929083;
    font-size: 10px;
    line-height: 1.9;
}

@media (max-width: 700px) {

    body {
        padding: 20px;
    }

    .register-container {
        grid-template-columns: 1fr;
    }

    .intro-panel {
        min-height: 340px;
        padding: 27px;
        border-right: none;
        border-bottom: 2px solid var(--border);
    }

    .intro-content {
        margin: 38px 0;
    }

    .intro-content h1 {
        font-size: 51px;
    }

    .deco {
        width: 55px;
        height: 55px;
        right: 25px;
        bottom: 55px;
    }

    .form-panel {
        padding: 35px 27px;
    }
}

</style>

</head>

<body>


<div class="register-container">


    <section class="intro-panel">


        <div>

            <div class="brand">
                ARSIP<span>.</span>
            </div>

            <div class="brand-subtitle">
                SISTEM INFORMASI SEKOLAH
            </div>

        </div>


        <div class="intro-content">

            <div class="eyebrow">
                STUDENT MANAGEMENT / 002
            </div>

            <h1>
                JOIN<br>ARSIP<span>.</span>
            </h1>

            <p>
                Buat akun administrator baru
                untuk mengelola data siswa
                dan informasi sekolah.
            </p>

            <div class="intro-tag">
                EST. 2026 — ARSIP SYSTEM
            </div>

        </div>


        <div class="intro-footer">
            DIGITAL SCHOOL MANAGEMENT<br>
            VERSION 1.0
        </div>


        <div class="deco"></div>


    </section>


    <section class="form-panel">


        <div class="form-label">
            ADMINISTRATOR / REGISTER
        </div>


        <h2>
            Create account.
        </h2>


        <p class="form-description">
            Silakan isi data untuk membuat
            akun administrator baru.
        </p>


        <?php if ($error !== ""): ?>

            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="">


            <div class="form-group">

                <label for="nama">
                    NAMA
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama"
                    value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="username">
                    USERNAME
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    autocomplete="username"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    PASSWORD
                </label>

                <div class="password-box">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Buat password"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('password', this)"
                    >
                        LIHAT
                    </button>

                </div>

            </div>


            <div class="form-group">

                <label for="konfirmasi">
                    KONFIRMASI PASSWORD
                </label>

                <input
                    type="password"
                    id="konfirmasi"
                    name="konfirmasi"
                    placeholder="Ulangi password"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="register-button"
            >
                BUAT AKUN →
            </button>


        </form>


        <div class="login-link">

            <a href="login.php">
                SUDAH PUNYA AKUN? LOGIN
            </a>

        </div>


        <div class="form-footer">

            ARSIP — STUDENT MANAGEMENT SYSTEM<br>
            © 2026 ALL RIGHTS RESERVED.

        </div>


    </section>


</div>


<script>

function togglePassword(id, button) {

    const password = document.getElementById(id);

    if (password.type === "password") {

        password.type = "text";
        button.textContent = "TUTUP";

    } else {

        password.type = "password";
        button.textContent = "LIHAT";

    }

}

</script>


</body>
</html>