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
    "expires_in": 3600,
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
    { "id": 1, "nim": "2301001", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
  ]
}
```

**Response 200 OK** (dengan `?id=1`):
```json
{
  "success": true,
  "data": { "id": 1, "nim": "2301001", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
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
| nim   | string | Ya    | Harus berupa angka, unik  |
| nama  | string | Ya    | Nama mahasiswa            |
| prodi | string | Ya    | Program studi             |

```json
{ "nim": "2301001", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Data mahasiswa berhasil ditambahkan",
  "data": { "id": 1, "nim": "2301001", "nama": "Budi Santoso", "prodi": "Teknik Informatika" }
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
    "nim": "NIM harus berupa angka",
    "nama": "Field nama wajib diisi",
    "prodi": "Field prodi wajib diisi"
  }
}
```

**Response 409 Conflict** (NIM sudah terdaftar):
```json
{ "success": false, "message": "NIM sudah terdaftar" }
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
{ "nim": "2301001", "nama": "Budi Santoso", "prodi": "Sistem Informasi" }
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

**Response 409 Conflict** (NIM dipakai mahasiswa lain):
```json
{ "success": false, "message": "NIM sudah terdaftar" }
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