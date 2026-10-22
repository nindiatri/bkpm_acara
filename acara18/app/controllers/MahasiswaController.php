
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
                'message' => 'Data mahasiswa berhasil diambil',
                'data' => $data
            ]);
        }

        Response::json(200, [
            'success' => true,
            'message' => 'Daftar mahasiswa berhasil diambil',
            'data' => $this->model->getAll()
        ]);
    }

    public function store(): void
    {
        $data = $this->getInput();
        $errors = $this->validate($data);

        if ($errors !== []) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal'
            ], $errors);
        }

        try {
            $id = $this->model->create($data);
        } catch (DomainException $e) {
            Response::json(409, [
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (PDOException $e) {
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
        $id = (int) $id;

        if (!$this->model->find($id)) {
            Response::json(404, [
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ]);
        }

        $data = $this->getInput();
        $errors = $this->validate($data, $id);

        if ($errors !== []) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal'
            ], $errors);
        }

        try {
            $this->model->update($id, $data);
        } catch (DomainException $e) {
            Response::json(409, [
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (PDOException $e) {
            $this->handleDbError($e);
        }

        Response::json(200, [
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui'
        ]);
    }

    public function destroy($id): void
    {
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
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            Response::json(400, [
                'success' => false,
                'message' => 'Body request harus berupa JSON yang valid'
            ]);
        }

        return $data;
    }

    private function validate(array $data, ?int $exceptId = null): array
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

        if (isset($data['nim']) && trim((string) $data['nim']) !== '') {
            $nim = trim((string) $data['nim']);
            if (!ctype_digit($nim) || strlen($nim) !== 9) {
                $errors['nim'] = 'NIM harus terdiri dari 9 digit angka';
            } elseif ($this->model->nimExists($nim, $exceptId)) {
                $errors['nim'] = 'NIM sudah terdaftar';
            }
        }

        return $errors;
    }

    private function handleDbError(PDOException $e): never
    {
        if ($e->getCode() === '23000') {
            Response::json(409, [
                'success' => false,
                'message' => 'NIM sudah terdaftar'
            ]);
        }

        error_log($e->getMessage());

        Response::json(500, [
            'success' => false,
            'message' => 'Terjadi kesalahan pada server'
        ]);
    }
}
