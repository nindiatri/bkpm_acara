<?php
class MahasiswaController
{
    private Mahasiswa $model;

    public function __construct()
    {
        $this->model = new Mahasiswa();
    }

    public function index(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id !== null) {
            if (!ctype_digit((string) $id)) {
                Response::json(400, [
                    'success' => false,
                    'message' => 'Parameter id harus berupa angka'
                ]);
            }

            $data = $this->model->find((int) $id);

            if (!$data) {
                Response::json(404, [
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ]);
            }

            Response::json(200, [
                'success' => true,
                'data' => $data
            ]);
        }

        Response::json(200, [
            'success' => true,
            'data' => $this->model->getAll()
        ]);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        $data = $this->getInput();
        $errors = $this->validate($data);

        if ($errors !== []) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => $errors
            ]);
        }

        // Acara 26: proses upload foto (opsional) setelah data teks valid
        $fotoBaru = $this->prosesUploadFoto();
        $data['foto'] = $fotoBaru;

        try {
            $id = $this->model->create($data);
        } catch (DomainException $e) {
            $this->hapusBerkas($fotoBaru); // batalkan upload bila simpan gagal
            // NIM duplikat = data tidak dapat diproses -> 422 (sesuai TC-03/TC-09)
            Response::json(422, [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => ['nim' => $e->getMessage()]
            ]);
        } catch (PDOException $e) {
            $this->hapusBerkas($fotoBaru);
            $this->handleDbError($e);
        }

        // Ambil kembali data mahasiswa yang baru ditambahkan
        $mahasiswa = $this->model->find((int) $id);

        Response::json(201, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan',
            'data' => $mahasiswa
        ]);
    }

    public function update($id): void
    {
        Auth::requireAdmin();
        $id = (int) $id;

        $lama = $this->model->find($id);
        if (!$lama) {
            Response::json(404, [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ]);
        }

        $data = $this->getInput();
        $errors = $this->validate($data);

        if ($errors !== []) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => $errors
            ]);
        }

        // Default: pertahankan foto lama. Jika ada foto baru yang valid, ganti.
        $fotoLama = $lama['foto'] ?? null;
        $fotoBaru = $this->prosesUploadFoto();
        $data['foto'] = $fotoBaru ?? $fotoLama;

        try {
            $this->model->update($id, $data);
        } catch (DomainException $e) {
            $this->hapusBerkas($fotoBaru);
            // NIM duplikat = data tidak dapat diproses -> 422 (sesuai TC-03/TC-09)
            Response::json(422, [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => ['nim' => $e->getMessage()]
            ]);
        } catch (PDOException $e) {
            $this->hapusBerkas($fotoBaru);
            $this->handleDbError($e);
        }

        // Tugas mandiri 1: hapus foto lama dengan unlink() setelah update berhasil
        if ($fotoBaru !== null && !empty($fotoLama)) {
            $this->hapusBerkas($fotoLama);
        }

        Response::json(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui',
            'data' => $this->model->find($id)
        ]);
    }

    public function destroy($id): void
    {
        Auth::requireAdmin();
        $id = (int) $id;

        if (!$this->model->find($id)) {
            Response::json(404, [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ]);
        }

        $this->model->delete($id);

        Response::json(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus'
        ]);
    }

    private function getInput(): array
    {
        // Acara 26: request multipart/form-data (dari FormData) dibaca dari $_POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'multipart/form-data') !== false) {
            return $_POST;
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, [
                'success' => false,
                'message' => 'Body request harus berupa JSON atau form-data yang valid'
            ]);
        }

        return $data;
    }

    private function uploadDir(): string
    {
        return __DIR__ . '/../../public/uploads/';
    }

    /**
     * Acara 26, Langkah 4: validasi dan simpan foto.
     * Mengembalikan nama berkas baru, atau null bila tidak ada foto diunggah.
     */
    private function prosesUploadFoto(): ?string
    {
        if (empty($_FILES['foto'])) {
            return null;
        }

        $file = $_FILES['foto'];

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // form dikirim tanpa memilih foto
        }

        if (
            $file['error'] === UPLOAD_ERR_INI_SIZE ||
            $file['error'] === UPLOAD_ERR_FORM_SIZE
        ) {
            $this->tolakUpload('Ukuran berkas maksimal 2MB');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->tolakUpload('Upload berkas gagal, silakan coba lagi');
        }

        // 1) Ekstensi
        $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ekstensi, ['jpg', 'jpeg', 'png'], true)) {
            $this->tolakUpload('Berkas harus berupa gambar JPG/PNG yang valid');
        }

        // 2) Signature berkas yang sebenarnya (ekstensi/type dari client bisa dipalsukan)
        $tipe = function_exists('exif_imagetype')
            ? @exif_imagetype($file['tmp_name'])
            : (@getimagesize($file['tmp_name'])[2] ?? false);

        if (!in_array($tipe, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            $this->tolakUpload('Berkas harus berupa gambar JPG/PNG yang valid');
        }

        // 3) Ukuran maksimal 2 MB
        if ($file['size'] > 2 * 1024 * 1024) {
            $this->tolakUpload('Ukuran berkas maksimal 2MB');
        }

        // 4) Nama baru unik agar tidak saling menimpa
        $namaBaru = uniqid('foto_', true) . '.' . $ekstensi;

        if (!move_uploaded_file($file['tmp_name'], $this->uploadDir() . $namaBaru)) {
            Response::json(500, [
                'success' => false,
                'message' => 'Gagal menyimpan berkas'
            ]);
        }

        return $namaBaru;
    }

    private function tolakUpload(string $pesan): never
    {
        Response::json(422, [
            'success' => false,
            'message' => $pesan,
            'errors' => ['foto' => $pesan]
        ]);
    }

    // Menghapus berkas foto dengan unlink(); basename() mencegah path traversal
    private function hapusBerkas(?string $nama): void
    {
        if (empty($nama)) {
            return;
        }

        $path = $this->uploadDir() . basename($nama);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function validate(array $data): array
    {
        $errors = [];

        foreach (['nim', 'nama', 'prodi'] as $field) {
            if (
                !isset($data[$field]) ||
                trim((string) $data[$field]) === ''
            ) {
                $errors[$field] = "Field {$field} wajib diisi";
            }
        }

        if (
            isset($data['nim']) &&
            trim((string) $data['nim']) !== '' &&
            !ctype_digit((string) $data['nim'])
        ) {
            $errors['nim'] = 'NIM harus berupa angka';
        }

        return $errors;
    }

    private function handleDbError(PDOException $e): never
    {
        if ($e->getCode() === '23000') {
            Response::json(422, [
                'success' => false,
                'message' => 'NIM sudah terdaftar',
                'errors' => ['nim' => 'NIM sudah terdaftar']
            ]);
        }

        error_log($e->getMessage());

        Response::json(500, [
            'success' => false,
            'message' => 'Terjadi kesalahan pada server'
        ]);
    }
}
