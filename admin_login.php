<?php
session_start();
require_once "config/koneksi.php";

if (isset($_SESSION["id_admin"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Username dan password harus diisi.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM admin WHERE username = ? LIMIT 1"
        );

        if (!$stmt) {

            $error = "Terjadi kesalahan pada sistem. Silakan coba lagi.";

        } else {

            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $admin = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if (!$admin || $password !== "230410") {

                $error = "Username atau password salah!";

            } else {

                session_regenerate_id(true);

                $_SESSION["id_admin"] = $admin["id_admin"];
                $_SESSION["username"] = $admin["username"];

                $_SESSION["nama_admin"] =
                    $admin["nama_admin"]
                    ?? $admin["username"];

                $_SESSION["role"] = "admin";

                unset(
                    $_SESSION["id_user"],
                    $_SESSION["nama_user"]
                );

                header("Location: index.php");
                exit;
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

<title>Admin Login | TITIKTEMU</title>

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

.login-container {
    width: 100%;
    max-width: 1000px;
    min-height: 590px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--surface);
    border: 2px solid var(--border);
    box-shadow: 8px 8px 0 var(--border);
}

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

.form-panel {
    padding: 55px 48px;
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
    margin-bottom: 32px;
}

.form-group {
    margin-bottom: 22px;
}

.form-group label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
    margin-bottom: 10px;
}

.form-group input {
    width: 100%;
    height: 49px;
    border: 1.5px solid var(--border);
    background: #faf9f4;
    padding: 0 14px;
    font-family: inherit;
    font-size: 13px;
    color: var(--text);
    outline: none;
}

.form-group input:focus {
    background: white;
    box-shadow: 3px 3px 0 var(--sage-dark);
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

.toggle-password:hover {
    background: var(--pink);
}

.login-button {
    width: 100%;
    padding: 16px;
    margin-top: 8px;
    background: var(--sage);
    color: var(--text);
    border: 1.5px solid var(--border);
    box-shadow: 4px 4px 0 var(--border);
    font-family: inherit;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
}

.login-button:hover {
    background: var(--pink);
}

.switch-login {
    text-align: center;
    margin-top: 20px;
    font-size: 11px;
    color: var(--muted);
}

.switch-login a {
    display: inline-block;
    margin-left: 5px;
    color: var(--text);
    font-weight: 800;
    text-decoration: none;
    border: 1px solid var(--border);
    padding: 5px 9px;
    background: var(--yellow);
    box-shadow: 2px 2px 0 var(--border);
}

.switch-login a:hover {
    background: var(--pink);
}

.error-message {
    padding: 13px;
    background: var(--pink-light);
    border: 1.5px solid var(--border);
    box-shadow: 3px 3px 0 var(--border);
    font-size: 12px;
    color: #854b43;
    font-weight: 600;
    margin-bottom: 22px;
}

.form-footer {
    border-top: 1.5px solid #dedbd1;
    padding-top: 18px;
    margin-top: 35px;
    color: #929083;
    font-size: 10px;
    line-height: 1.9;
}

@media (max-width: 700px) {

    body {
        padding: 20px;
    }

    .login-container {
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

<div class="login-container">

    <section class="intro-panel">

        <div>

            <div class="brand">
                TITIKTEMU<span>.</span>
            </div>

            <div class="brand-subtitle">
                SISTEM INFORMASI SEKOLAH
            </div>

        </div>

        <div class="intro-content">

            <div class="eyebrow">
                ADMIN MANAGEMENT / 001
            </div>

            <h1>
                ADMIN<br>LOGIN<span>.</span>
            </h1>

            <p>
                Halaman khusus administrator
                untuk mengelola sistem dan
                data siswa secara penuh.
            </p>

            <div class="intro-tag">
                ADMIN ACCESS — TITIKTEMU
            </div>

        </div>

        <div class="intro-footer">
            ADMINISTRATOR ACCESS<br>
            VERSION 1.0
        </div>

        <div class="deco"></div>

    </section>


    <section class="form-panel">

        <div class="form-label">
            ADMIN LOGIN / ACCESS
        </div>

        <h2>
            Sign in.
        </h2>

        <p class="form-description">
            Silakan masuk menggunakan akun
            administrator yang sudah terdaftar.
        </p>

        <?php if ($error !== ""): ?>

            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="username">
                    ADMIN USERNAME
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username admin"
                    autocomplete="username"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    ADMIN PASSWORD
                </label>

                <div class="password-box">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword()"
                    >
                        LIHAT
                    </button>

                </div>

            </div>

            <button
                type="submit"
                class="login-button"
            >
                MASUK SEBAGAI ADMIN →
            </button>

        </form>

        <div class="switch-login">
            Login sebagai?
            <a href="login.php">
                USER
            </a>
        </div>

        <div class="form-footer">
            TITIKTEMU — ADMINISTRATOR SYSTEM<br>
            © 2026 ALL RIGHTS RESERVED.
        </div>

    </section>

</div>

<script>

function togglePassword() {

    const password = document.getElementById("password");
    const button = document.querySelector(".toggle-password");

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
