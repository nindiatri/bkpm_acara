<?php
class BukuController
{
    private Buku $model;

    public function __construct()
    {
        $this->model = new Buku();
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
                    'message' => 'Data buku tidak ditemukan'
                ]);
            }

            Response::json(200, [
                'success' => true,
                'message' => 'Data buku berhasil diambil',
                'data' => $data
            ]);
        }

        Response::json(200, [
            'success' => true,
            'message' => 'Daftar buku berhasil diambil',
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

        $id = $this->model->create($data);
        $buku = $this->model->find((int) $id);

        Response::json(201, [
            'success' => true,
            'message' => 'Data buku berhasil ditambahkan',
            'data' => $buku
        ]);
    }

    public function update($id): void
    {
        $id = (int) $id;

        if (!$this->model->find($id)) {
            Response::json(404, [
                'success' => false,
                'message' => 'Data buku tidak ditemukan'
            ]);
        }

        $data = $this->getInput();
        $errors = $this->validate($data);

        if ($errors !== []) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal'
            ], $errors);
        }

        $this->model->update($id, $data);

        Response::json(200, [
            'success' => true,
            'message' => 'Data buku berhasil diperbarui'
        ]);
    }

    public function destroy($id): void
    {
        $id = (int) $id;

        if (!$this->model->find($id)) {
            Response::json(404, [
                'success' => false,
                'message' => 'Data buku tidak ditemukan'
            ]);
        }

        $this->model->delete($id);

        Response::json(200, [
            'success' => true,
            'message' => 'Data buku berhasil dihapus'
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

    private function validate(array $data): array
    {
        $errors = [];

        foreach (['judul', 'penulis', 'tahun_terbit'] as $field) {
            if (
                !isset($data[$field]) ||
                trim((string) $data[$field]) === ''
            ) {
                $errors[$field] = "Field {$field} wajib diisi";
            }
        }

        if (
            isset($data['tahun_terbit']) &&
            trim((string) $data['tahun_terbit']) !== '' &&
            !ctype_digit((string) $data['tahun_terbit'])
        ) {
            $errors['tahun_terbit'] = 'Tahun terbit harus berupa angka';
        } elseif (isset($data['tahun_terbit']) && trim((string) $data['tahun_terbit']) !== '') {
            $year = (int) $data['tahun_terbit'];
            if ($year < 1901 || $year > 2155) {
                $errors['tahun_terbit'] = 'Tahun terbit harus berada pada rentang 1901 sampai 2155';
            }
        }

        return $errors;
    }
}
