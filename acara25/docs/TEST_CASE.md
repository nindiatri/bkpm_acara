# TEST CASE - Acara 23: Manual Testing dan Blackbox Testing
## 1. Ruang Lingkup

| Acara | Controller | Endpoint | Fitur yang diuji |
|---|---|---|---|
| 17 | MahasiswaController | GET, POST, PUT, DELETE /mahasiswa | CRUD dasar, validasi field wajib, NIM duplikat, ID tidak ditemukan |
| 18 | MahasiswaController | GET, POST, PUT, DELETE /mahasiswa | Response seragam, validasi 422 (NIM 9 digit), 400/404/405 |
| 19 | AuthController | POST /register | Registrasi, validasi email, email duplikat |
| 19 | MahasiswaController | POST /mahasiswa | Regresi validasi NIM dari Acara 18 |
| 20 | AuthController | POST /login | Login, token JWT 5 menit |
| 20 | MahasiswaController | POST, PUT, DELETE /mahasiswa | Proteksi token (401), GET publik, regresi validasi NIM |
| 23 | Auth + Mahasiswa | Seluruh endpoint | Tabel test case BKPM Acara 23 + update/destroy + regresi |

## 2. Hasil Eksekusi Acara 17

| ID | Skenario | Request | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|
| A17-01 | Menampilkan seluruh data (index) | GET /mahasiswa | - | 200 OK | 200 | Pass |
| A17-02 | Menambah data valid (store) | POST /mahasiswa | nim=220100301 | 201 Created | 201 - Data berhasil ditambahkan | Pass |
| A17-03 | Menampilkan satu data (find) | GET /mahasiswa?id=8 | id=8 | 200 OK | 200 | Pass |
| A17-04 | Menampilkan data, ID tidak ada | GET /mahasiswa?id=99999 | id=99999 | 404 Not Found | 404 - Data tidak ditemukan | Pass |
| A17-05 | Menambah data dengan NIM kosong | POST /mahasiswa | nim="" | 400/422 | 400 - Field nim, nama, dan prodi wajib diisi | Pass |
| A17-06 | Menambah data dengan NIM duplikat | POST /mahasiswa | nim=220100301 (sudah ada) | 409/422 | 409 - NIM sudah terdaftar | Pass |
| A17-07 | Mengubah data valid (update) | PUT /mahasiswa?id=8 | nama diubah | 200 OK | 200 - Data berhasil diperbarui | Pass |
| A17-08 | Mengubah data, ID tidak ada | PUT /mahasiswa?id=99999 | id=99999 | 404 Not Found | 404 - Data tidak ditemukan | Pass |
| A17-09 | Menghapus data valid (destroy) | DELETE /mahasiswa?id=8 | id=8 | 200 OK | 200 - Data berhasil dihapus | Pass |
| A17-10 | Verifikasi data terhapus (GET setelah DELETE) | GET /mahasiswa?id=8 | id=8 | 404 Not Found | 404 - Data tidak ditemukan | Pass |
| A17-11 | Menghapus data, ID tidak ada | DELETE /mahasiswa?id=99999 | id=99999 | 404 Not Found | 404 - Data tidak ditemukan | Pass |

## 3. Hasil Eksekusi Acara 18

| ID | Skenario | Request | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|
| A18-01 | Format response seragam (success, message, data, errors, timestamp) | GET /mahasiswa | - | 200 OK + 5 field envelope | 200 - field envelope lengkap | Pass |
| A18-02 | NIM 9 digit valid | POST /mahasiswa | nim=220100311 | 201 Created | 201 - Data mahasiswa berhasil ditambahkan | Pass |
| A18-03 | NIM kosong | POST /mahasiswa | nim="" | 422 Unprocessable | 422 - Validasi data gagal | Pass |
| A18-04 | NIM kurang dari 9 digit | POST /mahasiswa | nim=2201003 (7 digit) | 422 Unprocessable | 422 - Validasi data gagal | Pass |
| A18-05 | NIM mengandung huruf | POST /mahasiswa | nim=22010031A | 422 Unprocessable | 422 - Validasi data gagal | Pass |
| A18-06 | NIM duplikat | POST /mahasiswa | nim=220100311 (sudah ada) | 422 Unprocessable | 422 - Validasi data gagal | Pass |
| A18-07 | ID bukan angka | GET /mahasiswa?id=abc | id=abc | 400 Bad Request | 400 - Parameter id harus berupa angka | Pass |
| A18-08 | Update, ID tidak ada | PUT /mahasiswa?id=99999 | id=99999 | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass |
| A18-09 | Body request bukan JSON | POST /mahasiswa | body={bukan json | 400 Bad Request | 400 - Body request harus berupa JSON yang valid | Pass |
| A18-10 | Endpoint tidak terdaftar (Router) | GET /endpoint-tidak-ada | - | 404 Not Found | 404 - Endpoint tidak ditemukan | Pass |
| A18-11 | Method tidak diizinkan (Router) | PATCH /mahasiswa?id=1 | - | 405 Method Not Allowed | 405 - Method tidak diizinkan | Pass |
| A18-12 | Hapus data valid | DELETE /mahasiswa?id=8 | id=8 | 200 OK | 200 - Data mahasiswa berhasil dihapus | Pass |
| A18-13 | Hapus, ID tidak ada | DELETE /mahasiswa?id=99999 | id=99999 | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass |

## 4. Hasil Eksekusi Acara 19 (temuan, kode tidak diubah)

| ID | Skenario | Request | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|
| A19-01 | Register data valid | POST /register | nama, email, password valid | 201 Created | 201 - Registrasi berhasil | Pass |
| A19-02 | Register field kosong | POST /register | semua field kosong | 400/422 | 400 - Nama, email, dan password wajib diisi | Pass |
| A19-03 | Register email format salah (filter_var) | POST /register | email=bukan-email | 400/422 | 400 - Format email tidak valid | Pass |
| A19-04 | Register email duplikat | POST /register | email sudah terdaftar | 400/409/422 | 400 - Registrasi gagal. Email mungkin sudah digunakan. | Pass |
| A19-05 | Register dengan method GET | GET /register | - | 405 Method Not Allowed | 405 - Method tidak diizinkan | Pass |
| A19-06 | Regresi: tambah mahasiswa NIM 9 digit | POST /mahasiswa | nim=220100321 | 201 Created | 201 - Data mahasiswa berhasil ditambahkan | Pass |
| A19-07 | Regresi: NIM kurang dari 9 digit (aturan Acara 18) | POST /mahasiswa | nim=2201003 (7 digit) | 422 Unprocessable | 201 - Data mahasiswa berhasil ditambahkan | Fail |
| A19-08 | Regresi: NIM duplikat (aturan Acara 18) | POST /mahasiswa | nim=220100321 (sudah ada) | 422 Unprocessable | 409 - NIM sudah terdaftar | Fail |

## 5. Hasil Eksekusi Acara 20 (temuan, kode tidak diubah)

| ID | Skenario | Request | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|
| A20-01 | Login kredensial benar | POST /login | email & password benar | 200 OK + token | 200 - token diterima | Pass |
| A20-02 | Login password salah | POST /login | password salah | 401 Unauthorized | 401 - Email atau password salah | Pass |
| A20-03 | Login email tidak terdaftar | POST /login | email tidak terdaftar | 401 Unauthorized | 401 - Email atau password salah | Pass |
| A20-04 | Login field kosong | POST /login | email & password kosong | 400/422 | 422 - Email dan password wajib diisi | Pass |
| A20-05 | Tambah mahasiswa tanpa token | POST /mahasiswa | tanpa Authorization | 401 Unauthorized | 401 - Token tidak ditemukan | Pass |
| A20-06 | Tambah mahasiswa dengan token diubah | POST /mahasiswa | token tidak valid | 401 Unauthorized | 401 - Token tidak valid atau kedaluwarsa | Pass |
| A20-07 | Tambah mahasiswa dengan token kedaluwarsa | POST /mahasiswa | token bertanda tangan sah, exp sudah lewat | 401 Unauthorized | 401 - Token tidak valid atau kedaluwarsa | Pass |
| A20-08 | Tambah mahasiswa dengan token valid | POST /mahasiswa | nim=220100331, token valid | 201 Created | 201 - Data mahasiswa berhasil ditambahkan | Pass |
| A20-09 | Ubah mahasiswa tanpa token | PUT /mahasiswa?id=8 | tanpa Authorization | 401 Unauthorized | 401 - Token tidak ditemukan | Pass |
| A20-10 | Hapus mahasiswa tanpa token | DELETE /mahasiswa?id=8 | tanpa Authorization | 401 Unauthorized | 401 - Token tidak ditemukan | Pass |
| A20-11 | Lihat mahasiswa tanpa token (GET tetap publik) | GET /mahasiswa | tanpa Authorization | 200 OK | 200 | Pass |
| A20-12 | Regresi: NIM kurang dari 9 digit dengan token valid | POST /mahasiswa | nim=2201003 (7 digit) | 422 Unprocessable | 201 - Data mahasiswa berhasil ditambahkan | Fail |
| A20-13 | Regresi: NIM duplikat dengan token valid | POST /mahasiswa | nim=220100331 (sudah ada) | 422 Unprocessable | 409 - NIM sudah terdaftar | Fail |
| A20-14 | Konsistensi expires_in login dengan masa berlaku token (5 menit) | POST /login | - | expires_in = 300 | 200 - expires_in = 3600 | Fail |

## 6. Hasil Eksekusi Acara 23 (TC-01 s.d. TC-22, sebelum perbaikan)

| ID | Skenario | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | NIM valid dan lengkap (store) | nim=220100123 | 201 Created | 201 - Data mahasiswa berhasil ditambahkan | Pass |
| TC-02 | NIM kosong (store) | nim="" | 400/422 | 422 - Validasi data gagal | Pass |
| TC-03 | NIM duplikat (store) | nim milik data lain (220100123) | 422 Unprocessable | 409 - NIM sudah terdaftar | Fail |
| TC-04 | Login kredensial benar | email & password benar | 200 OK, token diterima | 200 - Login berhasil, token ada | Pass |
| TC-05 | Login kredensial salah | password salah | 401 Unauthorized | 401 - Email atau password salah | Pass |
| TC-06 | Store tanpa token (requireAuth) | tanpa header Authorization | 401 Unauthorized | 401 - Token tidak ditemukan | Pass |
| TC-07 | Update data valid (NIM tetap milik sendiri) | id=1, nama diubah, nim sama | 200 OK | 200 - Data mahasiswa berhasil diperbarui | Pass |
| TC-08 | Update dengan NIM kosong | id=1, nim="" | 422 Unprocessable | 422 - Validasi data gagal | Pass |
| TC-09 | Update dengan NIM milik mahasiswa lain | id=1, nim=220100124 | 422 Unprocessable | 409 - NIM sudah terdaftar | Fail |
| TC-10 | Update ID tidak ada | id=99999 | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass |
| TC-11 | Update tanpa token | id=1, tanpa Authorization | 401 Unauthorized | 401 - Token tidak ditemukan | Pass |
| TC-12 | Update NIM mengandung huruf | id=1, nim=ABC123 | 422 Unprocessable | 422 - Validasi data gagal | Pass |
| TC-13 | Update ID bukan angka | id=abc | 400 Bad Request | 400 - Parameter id wajib diisi dan harus berupa angka | Pass |
| TC-14 | Update body bukan JSON valid | body={bukan json | 400 Bad Request | 400 - Body request harus berupa JSON yang valid | Pass |
| TC-15 | Hapus tanpa token | id=3, tanpa Authorization | 401 Unauthorized | 401 - Token tidak ditemukan | Pass |
| TC-16 | Hapus dengan token tidak valid | id=3, token diubah | 401 Unauthorized | 401 - Token tidak valid atau kedaluwarsa | Pass |
| TC-17 | Hapus ID bukan angka | id=abc | 400 Bad Request | 400 - Parameter id wajib diisi dan harus berupa angka | Pass |
| TC-18 | Hapus ID tidak ada | id=99999 | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass |
| TC-19 | Hapus data valid | id=3, token valid | 200 OK | 200 - Data mahasiswa berhasil dihapus | Pass |
| TC-20 | Verifikasi data terhapus (GET setelah DELETE) | id=3 | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass |
| TC-21 | NIM kurang dari 9 digit (store) | nim=2201003 (7 digit) | 422 Unprocessable | 201 - Data mahasiswa berhasil ditambahkan | Fail |
| TC-22 | Konsistensi expires_in login dengan masa berlaku token (5 menit) | - | expires_in = 300 | 200 - expires_in = 3600 | Fail |

Catatan: TC-21 dan TC-22 adalah uji regresi atas temuan Acara 19 dan 20 (kode Acara 20 dan 23 identik).

## 7. Analisis Test Case Fail dan Perbaikan (Acara 23)

| Test case | Acara | Gejala | Penyebab | Perbaikan |
|---|---|---|---|---|
| A19-07, A20-12, TC-21 | 19, 20, 23 | NIM 7 digit diterima (201) | `validate()` hanya memeriksa angka; aturan `strlen == 9` dari Acara 18 hilang | Tambah pemeriksaan 9 digit di `validate()` Acara 23 |
| A19-08, A20-13, TC-03, TC-09 | 19, 20, 23 | NIM duplikat dibalas 409 | Duplikat ditangani Model (`DomainException`) dan dibalas 409, bukan 422 | Ubah menjadi 422 + `errors.nim` pada Acara 23 |
| A20-14, TC-22 | 20, 23 | `expires_in` = 3600 | `AuthController::login` melaporkan 3600 padahal token berlaku 300 detik | Ubah menjadi 300 pada Acara 23 |

Berkas yang diubah (hanya Acara 23): `MahasiswaController.php` dan `AuthController.php`. Lapisan Model tidak diubah. `docs/API_DOCUMENTATION.md` diperbarui (NIM 9 digit, `expires_in` 300, status 422).

## 8. Hasil Retest Acara 23 Setelah Perbaikan

| ID | Skenario | Hasil Diharapkan | Hasil Aktual (retest) | Status Awal | Status Akhir |
|---|---|---|---|---|---|
| TC-01 | NIM valid dan lengkap (store) | 201 Created | 201 - Data mahasiswa berhasil ditambahkan | Pass | Pass |
| TC-02 | NIM kosong (store) | 400/422 | 422 - Validasi data gagal | Pass | Pass |
| TC-03 | NIM duplikat (store) | 422 Unprocessable | 422 - NIM sudah terdaftar | Fail | Pass |
| TC-04 | Login kredensial benar | 200 OK, token diterima | 200 - Login berhasil, token ada | Pass | Pass |
| TC-05 | Login kredensial salah | 401 Unauthorized | 401 - Email atau password salah | Pass | Pass |
| TC-06 | Store tanpa token (requireAuth) | 401 Unauthorized | 401 - Token tidak ditemukan | Pass | Pass |
| TC-07 | Update data valid (NIM tetap milik sendiri) | 200 OK | 200 - Data mahasiswa berhasil diperbarui | Pass | Pass |
| TC-08 | Update dengan NIM kosong | 422 Unprocessable | 422 - Validasi data gagal | Pass | Pass |
| TC-09 | Update dengan NIM milik mahasiswa lain | 422 Unprocessable | 422 - NIM sudah terdaftar | Fail | Pass |
| TC-10 | Update ID tidak ada | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass | Pass |
| TC-11 | Update tanpa token | 401 Unauthorized | 401 - Token tidak ditemukan | Pass | Pass |
| TC-12 | Update NIM mengandung huruf | 422 Unprocessable | 422 - Validasi data gagal | Pass | Pass |
| TC-13 | Update ID bukan angka | 400 Bad Request | 400 - Parameter id wajib diisi dan harus berupa angka | Pass | Pass |
| TC-14 | Update body bukan JSON valid | 400 Bad Request | 400 - Body request harus berupa JSON yang valid | Pass | Pass |
| TC-15 | Hapus tanpa token | 401 Unauthorized | 401 - Token tidak ditemukan | Pass | Pass |
| TC-16 | Hapus dengan token tidak valid | 401 Unauthorized | 401 - Token tidak valid atau kedaluwarsa | Pass | Pass |
| TC-17 | Hapus ID bukan angka | 400 Bad Request | 400 - Parameter id wajib diisi dan harus berupa angka | Pass | Pass |
| TC-18 | Hapus ID tidak ada | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass | Pass |
| TC-19 | Hapus data valid | 200 OK | 200 - Data mahasiswa berhasil dihapus | Pass | Pass |
| TC-20 | Verifikasi data terhapus (GET setelah DELETE) | 404 Not Found | 404 - Data mahasiswa tidak ditemukan | Pass | Pass |
| TC-21 | NIM kurang dari 9 digit (store) | 422 Unprocessable | 422 - Validasi data gagal | Fail | Pass |
| TC-22 | Konsistensi expires_in login dengan masa berlaku token (5 menit) | expires_in = 300 | 200 - expires_in = 300 | Fail | Pass |

## 9. Rekapitulasi

| Acara | Jumlah TC | Pass Awal | Fail Awal | Pass Akhir | Fail Akhir |
|---|---|---|---|---|---|
| 17 | 11 | 11 | 0 | 11 | 0 |
| 18 | 13 | 13 | 0 | 13 | 0 |
| 19 | 8 | 6 | 2 | 6 | 2 |
| 20 | 14 | 11 | 3 | 11 | 3 |
| 23 | 22 | 18 | 4 | 22 | 0 |
| Total | 68 | 59 | 9 | 63 | 5 |

Fail akhir pada Acara 19 dan 20 adalah temuan yang tidak diperbaiki; seluruh test case Acara 23 berstatus Pass.

## Catatan

- Soal BKPM menulis kredensial login sebagai *username*, implementasi memakai **email**.
- Berkas `app/models/user.php` (Acara 19) dipanggil sebagai `User.php`: aman di Windows/XAMPP, gagal di server Linux. Disarankan menamainya `User.php`.
