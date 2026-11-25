
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = "Dashboard";
}

if (!isset($breadcrumb)) {
    $breadcrumb = "Dashboard";
}
?>

<header class="topbar">
    <div>
        <p class="breadcrumb">
            Halaman / <?= htmlspecialchars($breadcrumb) ?>
        </p>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <div class="admin-label">
        Administrator
    </div>
</header>
