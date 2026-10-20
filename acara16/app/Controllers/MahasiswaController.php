<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';
require_once __DIR__ . '/../Core/Logger.php';

class MahasiswaController extends BaseController
{
    private MahasiswaRepository $repo;
    private MahasiswaService $service;

    public function __construct(
        MahasiswaRepository $repo,
        MahasiswaService $service
    ) {
        $this->repo = $repo;
        $this->service = $service;
    }

    public function index(): void
    {
        try {
            $mahasiswa = $this->repo->all();

            $this->view('mahasiswa/index', [
                'mahasiswa' => $mahasiswa
            ]);
        } catch (Throwable $e) {
            Logger::error($e);

            http_response_code(500);
            echo 'Data mahasiswa gagal ditampilkan.';
        }
    }

    public function create(): void
    {
        $this->view('mahasiswa/create');
    }

    public function store(): void
    {
        try {
            $berhasil = $this->service->create($_POST);

            $_SESSION['flash'] = [
                'type' => $berhasil ? 'success' : 'error',
                'message' => $berhasil
                    ? 'Data mahasiswa berhasil ditambahkan.'
                    : 'Data mahasiswa gagal disimpan.'
            ];
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => $e->getMessage()
            ];
        } catch (Throwable $e) {
            Logger::error($e);

            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Data mahasiswa gagal disimpan.'
            ];
        }

        $this->redirect('/BkpmWebServer/acara16/public/mahasiswa');
    }

    public function edit(int $id): void
    {
        try {
            $mahasiswa = $this->repo->find($id);

            if (!$mahasiswa) {
                http_response_code(404);
                echo 'Data mahasiswa tidak ditemukan.';
                return;
            }

            $this->view('mahasiswa/edit', [
                'mahasiswa' => $mahasiswa
            ]);
        } catch (Throwable $e) {
            Logger::error($e);

            http_response_code(500);
            echo 'Data mahasiswa gagal ditampilkan.';
        }
    }

    public function update(int $id): void
    {
        try {
            $berhasil = $this->service->update($id, $_POST);

            $_SESSION['flash'] = [
                'type' => $berhasil ? 'success' : 'danger',
                'message' => $berhasil
                    ? 'Data mahasiswa berhasil diubah.'
                    : 'Data mahasiswa gagal disimpan.'
            ];
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => $e->getMessage()
            ];
        } catch (Throwable $e) {
            Logger::error($e);

            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Data mahasiswa gagal disimpan.'
            ];
        }

        $this->redirect('/BkpmWebServer/acara16/public/mahasiswa');
    }

    public function destroy(int $id): void
    {
        try {
            $berhasil = $this->repo->delete($id);

            $_SESSION['flash'] = [
                'type' => $berhasil ? 'success' : 'error',
                'message' => $berhasil
                    ? 'Data mahasiswa berhasil dihapus.'
                    : 'Data mahasiswa gagal dihapus.'
            ];
        } catch (Throwable $e) {
            Logger::error($e);

            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Data mahasiswa gagal dihapus.'
            ];
        }

        $this->redirect('/BkpmWebServer/acara16/public/mahasiswa');
    }
}
