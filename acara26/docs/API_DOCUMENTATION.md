# Dokumentasi API Sistem Informasi Akademik

**Base URL:** `http://localhost/BkpmWebServer/acara19/public`

//Tugas Praktik Mandiri
## Ringkasan Endpoint

| No | Method | URL | Controller | Autentikasi | Keterangan |
|----|--------|-----|------------|-------------|------------|
| 1 | POST | /register | AuthController::register() | Publik | Mendaftarkan pengguna baru |
| 2 | GET | /mahasiswa | MahasiswaController::index() | Publik | Menampilkan semua data mahasiswa, atau satu data dengan `?id=` |
| 3 | POST | /mahasiswa | MahasiswaController::store() | Token | Menambah data mahasiswa baru |
| 4 | PUT | /mahasiswa/{id} | MahasiswaController::update($id) | Token | Memperbarui data mahasiswa |
| 5 | DELETE | /mahasiswa/{id} | MahasiswaController::destroy($id) | Token | Menghapus data mahasiswa |

//Langkah 2 
## Peta Router

//Langkah 2 
## Peta Router 

| Resource (URL) | Controller                | Keterangan                                   |
|----------------|---------------------------|----------------------------------------------|
| /mahasiswa     | MahasiswaController       | CRUD data mahasiswa (index/store/update/destroy) |
| /login         | AuthController::login     | Autentikasi pengguna, menghasilkan token     |
| /register      | AuthController::register  | Pendaftaran pengguna baru                    |

//Langkah 3
## POST /register
Controller: AuthController::register()

**Autentikasi:** Publik (tidak perlu token)

**Headers:** Content-Type: application/json

**Request Body:**
| Field    | Tipe   | Wajib | Keterangan                  |
|----------|--------|-------|-----------------------------|
| nama     | string | Ya    | Nama pengguna               |
| email    | string | Ya    | Harus berformat email valid |
| password | string | Ya    | Password pengguna           |

```json
{ "nama": "Budi Santoso", "email": "budi@example.com", "password": "rahasia123" }
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": { }
}
```

**Response 422 Unprocessable Entity** (validasi gagal):
```json
{
  "success": false,
  "message": "Validasi data gagal",
  "errors": {
    "nama": "Nama wajib diisi",
    "email": "Format email tidak valid",
    "password": "Password wajib diisi"
  }
}
```

**Response 409 Conflict** (email sudah terdaftar):
```json
{
  "success": false,
  "message": "...",
  "errors": { "email": "..." }
}
```

**Response 400 Bad Request** (body bukan JSON valid):
```json
{ "success": false, "message": "Body request harus berupa JSON yang valid" }
```

**Response 405 Method Not Allowed** (selain POST):
```json
{ "success": false, "message": "Method tidak diizinkan" }
```

## POST /login
Controller: AuthController::login()

**Autentikasi:** Publik (tidak perlu token)

**Headers:** Content-Type: application/json

**Request Body:**
| Field    | Tipe   | Wajib | Keterangan        |
|----------|--------|-------|-------------------|
| email    | string | Ya    | Email terdaftar   |
| password | string | Ya    | Password pengguna |

```json
{ "email": "budi@example.com", "password": "rahasia123" }
```

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "eyJ...",
    "token_type": "Bearer",
    "expires_in": 300,
    "user": { "id": 1, "nama": "Budi Santoso", "email": "budi@example.com" }
  }
}
```

**Response 401 Unauthorized** (email atau password salah):
```json
{ "success": false, "message": "Email atau password salah" }
```

**Response 422 Unprocessable Entity** (field kosong):
```json
{ "success": false, "message": "Email dan password wajib diisi" }
```

**Response 400 Bad Request** (body bukan JSON valid):
```json
{ "success": false, "message": "Body request harus berupa JSON yang valid" }
```

**Response 405 Method Not Allowed** (selain POST):
```json
{ "success": false, "message": "Method tidak diizinkan" }
```

//Langkah 4
## GET /mahasiswa
Controller: MahasiswaController::index()

**Autentikasi:** Publik (tidak perlu token)

**Query Parameter (opsional):**
| Parameter | Tipe    | Keterangan                                              |
|-----------|---------|---------------------------------------------------------|
| id        | integer | Jika diisi, hanya mengembalikan satu mahasiswa dengan id tersebut |

Contoh: `GET /mahasiswa` (semua data) atau `GET /mahasiswa?id=1` (satu data)

**Response 200 OK** (semua data):
```json
{
  "success": true,
  "data": [
    { "id": 1, "nim": "230100101", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
  ]
}
```

**Response 200 OK** (dengan `?id=1`):
```json
{
  "success": true,
  "data": { "id": 1, "nim": "230100101", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
}
```

**Response 400 Bad Request** (id bukan angka):
```json
{ "success": false, "message": "Parameter id harus berupa angka" }
```

**Response 404 Not Found** (id tidak ditemukan):
```json
{ "success": false, "message": "Data mahasiswa tidak ditemukan" }
```

---

## POST /mahasiswa
Controller: MahasiswaController::store()

**Autentikasi:** Perlu token (`Auth::requireAuth()`)

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <token>

**Request Body:**
| Field | Tipe   | Wajib | Keterangan                |
|-------|--------|-------|---------------------------|
| nim   | string | Ya    | 9 digit angka, unik       |
| nama  | string | Ya    | Nama mahasiswa            |
| prodi | string | Ya    | Program studi             |

```json
{ "nim": "230100101", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Data mahasiswa berhasil ditambahkan",
  "data": { "id": 1, "nim": "230100101", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
}
```

**Response 401 Unauthorized** (token tidak ada atau tidak valid):
```json
{ "success": false, "message": "..." }
```

**Response 422 Unprocessable Entity** (validasi gagal):
```json
{
  "success": false,
  "message": "Validasi data gagal",
  "errors": {
    "nim": "NIM harus terdiri dari 9 digit angka",
    "nama": "Field nama wajib diisi",
    "prodi": "Field prodi wajib diisi"
  }
}
```

**Response 422 Unprocessable Entity** (NIM sudah terdaftar):
```json
{ "success": false, "message": "NIM sudah terdaftar", "errors": { "nim": "NIM sudah terdaftar" } }
```

**Response 400 Bad Request** (body bukan JSON valid):
```json
{ "success": false, "message": "Body request harus berupa JSON yang valid" }
```

**Response 500 Internal Server Error** (kesalahan database lainnya):
```json
{ "success": false, "message": "Terjadi kesalahan pada server" }
```

---

## PUT /mahasiswa/{id}
Controller: MahasiswaController::update($id)

**Autentikasi:** Perlu token (`Auth::requireAuth()`)

**Headers:**
- Content-Type: application/json
- Authorization: Bearer <token>

**Path Parameter:**
| Parameter | Tipe    | Keterangan                    |
|-----------|---------|-------------------------------|
| id        | integer | Id mahasiswa yang diperbarui  |

**Request Body:** sama dengan `POST /mahasiswa` (`nim`, `nama`, dan `prodi` semuanya wajib diisi).

```json
{ "nim": "230100101", "nama": "Budi Santoso", "prodi": "Sistem Informasi" }
```

**Response 200 OK:**
```json
{ "success": true, "message": "Data mahasiswa berhasil diperbarui" }
```

**Response 401 Unauthorized:** sama seperti `POST /mahasiswa`.

**Response 404 Not Found** (id tidak ditemukan):
```json
{ "success": false, "message": "Data mahasiswa tidak ditemukan" }
```

**Response 422 Unprocessable Entity:** sama seperti `POST /mahasiswa`.

**Response 422 Unprocessable Entity** (NIM dipakai mahasiswa lain):
```json
{ "success": false, "message": "NIM sudah terdaftar", "errors": { "nim": "NIM sudah terdaftar" } }
```

**Response 400 Bad Request** (body bukan JSON valid):
```json
{ "success": false, "message": "Body request harus berupa JSON yang valid" }
```

---

## DELETE /mahasiswa/{id}
Controller: MahasiswaController::destroy($id)

**Autentikasi:** Perlu token (`Auth::requireAuth()`)

**Headers:** Authorization: Bearer <token>

**Path Parameter:**
| Parameter | Tipe    | Keterangan                 |
|-----------|---------|----------------------------|
| id        | integer | Id mahasiswa yang dihapus  |

**Response 200 OK:**
```json
{ "success": true, "message": "Data mahasiswa berhasil dihapus" }
```

**Response 401 Unauthorized:** sama seperti `POST /mahasiswa`.

**Response 404 Not Found** (id tidak ditemukan):
```json
{ "success": false, "message": "Data mahasiswa tidak ditemukan" }
```

---

## Acara 26 - Upload Foto Mahasiswa

Kolom `foto` (VARCHAR 255, nullable) ditambahkan pada tabel `mahasiswa` (`sql/acara26_foto.sql`).
Berkas disimpan di `public/uploads/` dan dapat diakses di `{BASE_URL}/uploads/{nama_berkas}`.

### POST /mahasiswa (dengan foto)
**Autentikasi:** Token, role admin (`Auth::requireAdmin()`)

**Headers:** Authorization: Bearer <token> (Content-Type diisi otomatis `multipart/form-data`)

**Body (form-data):**
| Field | Tipe   | Wajib | Keterangan                          |
|-------|--------|-------|-------------------------------------|
| nim   | text   | Ya    | Angka, unik                         |
| nama  | text   | Ya    | Nama mahasiswa                      |
| prodi | text   | Ya    | Program studi                       |
| foto  | file   | Tidak | JPG/PNG valid, maksimal 2 MB        |

**Validasi foto:** ekstensi (jpg/jpeg/png), signature berkas (`exif_imagetype()`), ukuran <= 2 MB,
nama berkas diganti unik (`uniqid()`).

| Kode | Kondisi |
|------|---------|
| 201  | Data dan foto berhasil disimpan; `data.foto` berisi nama berkas |
| 401 / 403 | Token tidak ada/tidak valid / bukan admin |
| 422  | Berkas bukan gambar JPG/PNG valid, atau ukuran > 2 MB, atau validasi teks gagal |

### POST /mahasiswa?id={id}&_method=PUT (update dengan foto)
PHP tidak mengisi `$_POST`/`$_FILES` pada method PUT, sehingga update yang membawa berkas dikirim
dengan **POST** dan parameter `_method=PUT` (method override pada Router). Request PUT berbody JSON
tanpa foto tetap didukung seperti sebelumnya.

Jika foto baru valid, foto lama dihapus dengan `unlink()` setelah data berhasil diperbarui.
Jika `foto` tidak dikirim, foto lama dipertahankan.
