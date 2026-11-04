# Acara 20 - Lanjutan Acara 19

## Penting sebelum digunakan
- Project memakai database `db_workshop` dari konfigurasi `app/config/Database.php`.
- ZIP Acara 19 yang diberikan tidak menyertakan file SQL, jadi database dan tabel lama tidak diubah.
- Pastikan tabel `users` yang sudah dipakai saat registrasi Acara 19 memiliki kolom `id`, `nama`, `email`, dan `password`, serta kolom `email` unik.
- Sebelum dipakai di luar praktikum, ganti `SECRET_KEY` di `app/core/Auth.php` dengan secret acak yang panjang.

## URL
Ganti sesuai nama folder dan lokasi project. Jika folder project `acara19` berada di `htdocs`:
`http://localhost/acara19/public/login`
`http://localhost/acara19/public/mahasiswa`
Jika sebelumnya kamu memakai Environment Postman, atur `base_url` ke alamat folder `public`, misalnya `http://localhost/acara19/public`, lalu gunakan `{{base_url}}/login` dan `{{base_url}}/mahasiswa`.

## Login di Postman
- Method: POST
- URL: `{{base_url}}/login`
- Body > raw > JSON:
```json
{
  "email": "email-yang-didaftarkan@example.com",
  "password": "password-registrasi"
}
```
- Salin nilai `data.token` dari response.

## POST mahasiswa
- Method: POST
- URL: `{{base_url}}/mahasiswa`
- Headers: `Content-Type: application/json`, `Authorization: Bearer TEMPEL_TOKEN_DI_SINI`
- Body > raw > JSON:
```json
{
  "nim": "230099",
  "nama": "Mahasiswa Uji",
  "prodi": "Teknik Informatika"
}
```

## PUT mahasiswa
- Method: PUT
- URL: `{{base_url}}/mahasiswa?id=ID_MAHASISWA`
- Headers sama seperti POST.
- Body:
```json
{
  "nim": "230099",
  "nama": "Mahasiswa Uji Diubah",
  "prodi": "Teknik Informatika"
}
```

## DELETE mahasiswa
- Method: DELETE
- URL: `{{base_url}}/mahasiswa?id=ID_MAHASISWA`
- Header `Authorization: Bearer TEMPEL_TOKEN_DI_SINI`.

## Uji gagal
1. Hapus header Authorization, kirim POST/PUT/DELETE: response seharusnya HTTP 401.
2. Pasang `Authorization: Bearer token-sengaja-diubah`: response seharusnya HTTP 401.
3. GET `/mahasiswa` tetap publik, sesuai instruksi Acara 20.

## Catatan
Endpoint PUT dan DELETE menggunakan query parameter `?id=...`, karena itulah pola Router pada project Acara 19 ini.
