
CREATE DATABASE IF NOT EXISTS db_pengelolaan_siswa;

USE db_pengelolaan_siswa;

-- TABEL ADMIN
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nama_admin VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- TABEL SISWA
CREATE TABLE IF NOT EXISTS siswa (
    id_siswa INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(30) NOT NULL,
    nama_siswa VARCHAR(100) NOT NULL,
    kelas VARCHAR(30) NOT NULL,
    jenis_kelamin VARCHAR(20) NOT NULL,
    alamat TEXT
);

-- TABEL EKSTRAKURIKULER
CREATE TABLE IF NOT EXISTS ekstrakurikuler (
    id_ekskul INT AUTO_INCREMENT PRIMARY KEY,
    nama_ekskul VARCHAR(100) NOT NULL,
    pembina VARCHAR(100),
    keterangan TEXT
);

-- TABEL NILAI EKSTRAKURIKULER
CREATE TABLE IF NOT EXISTS nilai_ekskul (
    id_nilai INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    id_ekskul INT NOT NULL,
    nilai DECIMAL(5,2) NOT NULL,
    semester VARCHAR(20),
    tahun_ajaran VARCHAR(20),
    keterangan TEXT
);
