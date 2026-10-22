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
            $data = $this->model->find((int) $id);
            if (!$data) {
                $this->json(404, ['success' => false, 'message' => 'Data tidak ditemukan']);
            }
            $this->json(200, ['success' => true, 'data' => $data]);
        }

        $this->json(200, ['success' => true, 'data' => $this->model->getAll()]);
    }

    public function store(): void
    {
        $data = $this->getInput();

        if ($error = $this->validate($data)) {
            $this->json(400, ['success' => false, 'message' => $error]);
        }

        try {
            $id = $this->model->create($data);
        } catch (DomainException $e) {
            $this->json(409, ['success' => false, 'message' => $e->getMessage()]);
        } catch (PDOException $e) {
            $this->handleDbError($e);
        }

        $this->json(201, [
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data'    => ['id' => $id],
        ]);
    }

    public function update($id): void
    {
        $id = (int) $id;

        if (!$this->model->find($id)) {
            $this->json(404, ['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $data = $this->getInput();

        if ($error = $this->validate($data)) {
            $this->json(400, ['success' => false, 'message' => $error]);
        }

        try {
            $this->model->update($id, $data);
        } catch (DomainException $e) {
            $this->json(409, ['success' => false, 'message' => $e->getMessage()]);
        } catch (PDOException $e) {
            $this->handleDbError($e);
        }

        $this->json(200, ['success' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function destroy($id): void
    {
        $id = (int) $id;

        if (!$this->model->find($id)) {
            $this->json(404, ['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $this->model->delete($id);
        $this->json(200, ['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    /* ---------- helper ---------- */

    private function getInput(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            $this->json(400, ['success' => false, 'message' => 'Body request harus berupa JSON yang valid']);
        }
        return $data;
    }

    private function validate(array $data): ?string
    {
        foreach (['nim', 'nama', 'prodi'] as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                return 'Field nim, nama, dan prodi wajib diisi';
            }
        }
        return null;
    }

    private function handleDbError(PDOException $e): never
    {
        // SQLSTATE 23000 = pelanggaran constraint (mis. NIM duplikat lolos ke UNIQUE)
        if ($e->getCode() === '23000') {
            $this->json(409, ['success' => false, 'message' => 'NIM sudah terdaftar']);
        }
        error_log($e->getMessage());
        $this->json(500, ['success' => false, 'message' => 'Terjadi kesalahan pada server']);
    }

    private function json(int $status, array $payload): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}