-- Acara 25: menambahkan kolom role pada tabel users
-- Jalankan di phpMyAdmin (tab SQL) pada database db_workshop.
-- Cek dulu apakah kolom sudah ada:  SHOW COLUMNS FROM users LIKE 'role';
-- Jika hasilnya sudah ada baris 'role', lewati perintah ALTER di bawah.

USE db_workshop;

ALTER TABLE users
    ADD COLUMN role ENUM('admin','user') NOT NULL DEFAULT 'user';

-- Jadikan satu akun sebagai admin (ganti email sesuai akun hasil POST /register)
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';

-- Pastikan akun lain berperan user
-- UPDATE users SET role = 'user' WHERE email = 'user@example.com';

SELECT id, nama, email, role FROM users;
