
<?php
session_start();
require_once "config/koneksi.php";

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($nama === "" || $email === "" || $username === "" || $password === "") {
        $pesan = "Semua kolom wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesan = "Format email tidak valid!";
    } elseif (strlen($password) < 8) {
        $pesan = "Password minimal 8 karakter!";
    } else {
        $cek = mysqli_prepare(
            $conn,
            "SELECT id_user FROM users WHERE email = ? OR username = ?"
        );

        mysqli_stmt_bind_param($cek, "ss", $email, $username);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            $pesan = "Email atau username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (nama, email, username, password, role)
                 VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $nama,
                $email,
                $username,
                $hash,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php?daftar=berhasil");
                exit;
            } else {
                $pesan = "Registrasi gagal!";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Pengguna | ARSIP.</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --background:#f7f3eb;
    --surface:#fffdf8;
    --sage:#dce6d3;
    --sage-dark:#819777;
    --pink:#edc8cb;
    --pink-light:#f8e5e3;
    --yellow:#f2dfa4;
    --text:#343a32;
    --muted:#88897e;
    --border:#373b33;
}

* {
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body {
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:35px 20px;
    background:var(--background);
    color:var(--text);
    font-family:'DM Sans',sans-serif;
}

.register-container {
    width:100%;
    max-width:1000px;
    display:grid;
    grid-template-columns:1fr 1fr;
    background:var(--surface);
    border:2px solid var(--border);
    box-shadow:8px 8px 0 var(--border);
    animation:appear .5s ease;
}

@keyframes appear {
    from {opacity:0;transform:translateY(15px)}
    to {opacity:1;transform:translateY(0)}
}

.intro-panel {
    background:var(--sage);
    padding:38px;
    border-right:2px solid var(--border);
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.brand {
    font-family:'Manrope',sans-serif;
    font-size:29px;
    font-weight:800;
    letter-spacing:-1.5px;
}

.brand span {
    color:var(--sage-dark);
}

.brand-subtitle {
    font-size:9px;
    letter-spacing:2px;
    margin-top:7px;
    color:#707969;
    font-weight:700;
}

.intro-content {
    margin:55px 0;
}

.eyebrow {
    display:inline-block;
    background:var(--surface);
    border:1.5px solid var(--border);
    padding:9px 11px;
    font-size:9px;
    letter-spacing:1.5px;
    font-weight:800;
    margin-bottom:24px;
    box-shadow:2px 2px 0 var(--border);
}

.intro-content h1 {
    font-family:'Manrope',sans-serif;
    font-size:clamp(48px,6vw,70px);
    line-height:.96;
    letter-spacing:-5px;
    font-weight:800;
}

.intro-content h1 span {
    color:var(--sage-dark);
}

.intro-content p {
    max-width:280px;
    font-size:13px;
    line-height:1.8;
    color:#60685a;
    margin-top:23px;
}

.intro-tag {
    display:inline-block;
    width:fit-content;
    padding:12px 15px;
    border:1.5px solid var(--border);
    background:var(--pink);
    box-shadow:3px 3px 0 var(--border);
    font-size:10px;
    font-weight:800;
    margin-top:27px;
}

.intro-footer {
    font-size:9px;
    letter-spacing:1.5px;
    line-height:1.9;
    color:#697263;
    font-weight:700;
}

.form-panel {
    padding:45px 48px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.form-label {
    font-size:10px;
    font-weight:800;
    letter-spacing:2px;
    color:var(--sage-dark);
    margin-bottom:17px;
}

.form-panel h2 {
    font-family:'Manrope',sans-serif;
    font-size:39px;
    font-weight:800;
    letter-spacing:-2px;
    margin-bottom:10px;
}

.form-description {
    color:var(--muted);
    font-size:13px;
    line-height:1.8;
    margin-bottom:23px;
}

.form-group {
    margin-bottom:15px;
}

.form-group label {
    display:block;
    font-size:10px;
    font-weight:800;
    letter-spacing:1.5px;
    margin-bottom:8px;
}

.form-group input {
    width:100%;
    height:45px;
    border:1.5px solid var(--border);
    background:#faf9f4;
    padding:0 14px;
    font-family:inherit;
    font-size:13px;
    color:var(--text);
    outline:none;
}

.form-group input:focus {
    background:white;
    box-shadow:3px 3px 0 var(--sage-dark);
}

.register-button {
    width:100%;
    padding:15px;
    margin-top:8px;
    background:var(--sage);
    color:var(--text);
    border:1.5px solid var(--border);
    box-shadow:4px 4px 0 var(--border);
    font-family:inherit;
    font-size:11px;
    font-weight:800;
    letter-spacing:1px;
    cursor:pointer;
    transition:.2s ease;
}

.register-button:hover {
    background:var(--pink);
    transform:translate(-2px,-2px);
    box-shadow:6px 6px 0 var(--border);
}

.error-message {
    padding:13px;
    background:var(--pink-light);
    border:1.5px solid var(--border);
    box-shadow:3px 3px 0 var(--border);
    font-size:12px;
    color:#854b43;
    font-weight:600;
    margin-bottom:18px;
}

.form-footer {
    border-top:1.5px solid #dedbd1;
    padding-top:18px;
    margin-top:25px;
    color:#929083;
    font-size:11px;
    line-height:1.9;
    text-align:center;
}

.form-footer a {
    color:var(--sage-dark);
    font-weight:800;
    text-decoration:underline;
}

@media(max-width:700px) {
    body {padding:20px}

    .register-container {
        grid-template-columns:1fr;
    }

    .intro-panel {
        min-height:280px;
        padding:27px;
        border-right:none;
        border-bottom:2px solid var(--border);
    }

    .intro-content {margin:35px 0}
    .intro-content h1 {font-size:51px}
    .form-panel {padding:35px 27px}
}
</style>
</head>

<body>

<div class="register-container">

    <section class="intro-panel">

        <div>
            <div class="brand">ARSIP<span>.</span></div>
            <div class="brand-subtitle">
                SISTEM INFORMASI SEKOLAH
            </div>
        </div>

        <div class="intro-content">
            <div class="eyebrow">
                STUDENT MANAGEMENT / 001
            </div>

            <h1>JOIN<br>US<span>.</span></h1>

            <p>
                Buat akun pengguna untuk melihat
                informasi dan perkembangan siswa
                melalui sistem ARSIP.
            </p>

            <div class="intro-tag">
                USER REGISTRATION
            </div>
        </div>

        <div class="intro-footer">
            DIGITAL SCHOOL MANAGEMENT<br>
            VERSION 1.0
        </div>

    </section>

    <section class="form-panel">

        <div class="form-label">USER / REGISTER</div>

        <h2>Create account.</h2>

        <p class="form-description">
            Silakan lengkapi data berikut untuk membuat akun pengguna.
        </p>

        <?php if ($pesan !== ""): ?>
            <div class="error-message" role="alert">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label for="nama">NAMA LENGKAP</label>
                <input type="text" id="nama" name="nama"
                       placeholder="Masukkan nama lengkap"
                       required>
            </div>

            <div class="form-group">
                <label for="email">EMAIL / GMAIL</label>
                <input type="email" id="email" name="email"
                       placeholder="Masukkan Gmail"
                       required>
            </div>

            <div class="form-group">
                <label for="username">USERNAME</label>
                <input type="text" id="username" name="username"
                       placeholder="Buat username"
                       required>
            </div>

            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input type="password" id="password" name="password"
                       placeholder="Minimal 8 karakter"
                       minlength="8"
                       required>
            </div>

            <button type="submit" class="register-button">
                DAFTAR SEKARANG →
            </button>

        </form>

        <div class="form-footer">
            Sudah punya akun?
            <a href="login.php">LOGIN DI SINI</a>
            <br>
            ARSIP — STUDENT MANAGEMENT SYSTEM
        </div>

    </section>

</div>

</body>
</html>
