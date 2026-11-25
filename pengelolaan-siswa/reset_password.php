
<?php
require_once "config/koneksi.php";

$pesan = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if ($password_baru === "" || $konfirmasi === "") {
        $pesan = "Semua kolom wajib diisi.";
        $status = "error";
    } elseif (strlen($password_baru) < 8) {
        $pesan = "Password minimal 8 karakter.";
        $status = "error";
    } elseif ($password_baru !== $konfirmasi) {
        $pesan = "Konfirmasi password tidak cocok.";
        $status = "error";
    } else {
        $username = "sheza";
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE admin SET password = ? WHERE username = ?"
        );

        mysqli_stmt_bind_param($stmt, "ss", $hash, $username);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $pesan = "Password berhasil diperbarui!";
                $status = "success";
            } else {
                $cek = mysqli_prepare(
                    $conn,
                    "SELECT id_admin FROM admin WHERE username = ?"
                );
                mysqli_stmt_bind_param($cek, "s", $username);
                mysqli_stmt_execute($cek);
                mysqli_stmt_store_result($cek);

                if (mysqli_stmt_num_rows($cek) > 0) {
                    $pesan = "Password sudah sama dengan sebelumnya.";
                    $status = "success";
                } else {
                    $pesan = "Akun admin tidak ditemukan.";
                    $status = "error";
                }

                mysqli_stmt_close($cek);
            }
        } else {
            $pesan = "Gagal memperbarui password.";
            $status = "error";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | ARSIP.</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --background: #f7f3eb;
    --surface: #fffdf8;
    --sage: #dce6d3;
    --sage-dark: #819777;
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
    padding: 25px;
    background: var(--background);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
}

.reset-container {
    width: 100%;
    max-width: 460px;
    padding: 42px;
    background: var(--surface);
    border: 2px solid var(--border);
    box-shadow: 7px 7px 0 var(--border);
    animation: appear .4s ease;
}

@keyframes appear {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.brand {
    font-family: 'Manrope', sans-serif;
    font-size: 25px;
    font-weight: 800;
    margin-bottom: 35px;
}

.brand span {
    color: var(--sage-dark);
}

.label {
    color: var(--sage-dark);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    margin-bottom: 12px;
}

h1 {
    font-family: 'Manrope', sans-serif;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 10px;
}

.description {
    font-size: 13px;
    color: var(--muted);
    line-height: 1.7;
    margin-bottom: 28px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
    margin-bottom: 9px;
}

input {
    width: 100%;
    height: 48px;
    padding: 0 13px;
    border: 1.5px solid var(--border);
    background: #faf9f4;
    outline: none;
    font-family: inherit;
    font-size: 13px;
}

input:focus {
    background: white;
    box-shadow: 3px 3px 0 var(--sage-dark);
}

.submit-button {
    width: 100%;
    padding: 15px;
    margin-top: 7px;
    border: 1.5px solid var(--border);
    background: var(--sage);
    box-shadow: 4px 4px 0 var(--border);
    color: var(--text);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
    transition: .2s;
}

.submit-button:hover {
    background: var(--yellow);
    transform: translate(-2px, -2px);
    box-shadow: 6px 6px 0 var(--border);
}

.message {
    padding: 13px;
    margin-bottom: 20px;
    border: 1.5px solid var(--border);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.6;
}

.message.success {
    background: var(--sage);
}

.message.error {
    background: var(--pink-light);
}

.back-link {
    display: block;
    margin-top: 25px;
    text-align: center;
    color: var(--sage-dark);
    font-size: 12px;
    font-weight: 800;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .reset-container {
        padding: 28px 23px;
    }
}
</style>
</head>

<body>

<div class="reset-container">

    <div class="brand">ARSIP<span>.</span></div>

    <div class="label">ACCOUNT RECOVERY / ADMIN</div>

    <h1>Reset Password.</h1>

    <p class="description">
        Buat password baru untuk akun administrator ARSIP.
    </p>

    <?php if ($pesan !== ""): ?>
        <div class="message <?= htmlspecialchars($status) ?>">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($status !== "success"): ?>

    <form method="POST">

        <div class="form-group">
            <label for="password_baru">PASSWORD BARU</label>
            <input
                type="password"
                name="password_baru"
                id="password_baru"
                placeholder="Minimal 8 karakter"
                minlength="8"
                required
            >
        </div>

        <div class="form-group">
            <label for="konfirmasi">KONFIRMASI PASSWORD</label>
            <input
                type="password"
                name="konfirmasi"
                id="konfirmasi"
                placeholder="Ulangi password baru"
                minlength="8"
                required
            >
        </div>

        <button type="submit" class="submit-button">
            SIMPAN PASSWORD BARU →
        </button>

    </form>

    <?php else: ?>

        <a href="login.php" class="back-link">
            KEMBALI KE LOGIN →
        </a>

    <?php endif; ?>

</div>

</body>
</html>
