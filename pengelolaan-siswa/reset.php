
<?php
require_once "config/koneksi.php";

$username = "sheza";
$passwordBaru = "12345";

$hash = password_hash($passwordBaru, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "UPDATE admin SET password = ? WHERE username = ?"
);

mysqli_stmt_bind_param($stmt, "ss", $hash, $username);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) >= 0) {
        echo "Password admin berhasil direset!";
        echo "<br>Username: sheza";
        echo "<br>Password: 12345";
    }
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>
