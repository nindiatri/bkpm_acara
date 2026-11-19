-- Acara 26, Langkah 1: menambahkan kolom foto pada tabel mahasiswa
-- Jalankan di phpMyAdmin (tab SQL) pada database db_workshop.
-- Cek dulu apakah kolom sudah ada:  SHOW COLUMNS FROM mahasiswa LIKE 'foto';
-- Jika hasilnya sudah ada baris 'foto', lewati perintah ALTER di bawah.

USE db_workshop;

ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi;

DESCRIBE mahasiswa;
