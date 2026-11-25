
<?php
$currentPage = $currentPage ?? '';
?>

<aside class="sidebar">

    <div class="brand">
        <h2>ARSIP<span>.</span></h2>
        <small>SISTEM INFORMASI SEKOLAH</small>
    </div>

    <p class="menu-label">MENU</p>

    <nav>
        <a href="/pengelolaan-siswa/index.php"
           class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            Dashboard
        </a>

        <a href="/pengelolaan-siswa/siswa/index.php"
           class="<?= $currentPage === 'siswa' ? 'active' : '' ?>">
            Data Siswa
        </a>

        <a href="/pengelolaan-siswa/ekstrakurikuler/index.php"
           class="<?= $currentPage === 'ekstrakurikuler' ? 'active' : '' ?>">
            Ekstrakurikuler
        </a>

        <a href="/pengelolaan-siswa/nilai/index.php"
           class="<?= $currentPage === 'nilai' ? 'active' : '' ?>">
            Data Nilai
        </a>

        <a href="/pengelolaan-siswa/laporan/index.php"
           class="<?= $currentPage === 'laporan' ? 'active' : '' ?>">
            Laporan
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="/pengelolaan-siswa/logout.php">Logout</a>
        <small>Pengelolaan Data Siswa</small>
        <small>Versi 1.0</small>
    </div>

</aside>
