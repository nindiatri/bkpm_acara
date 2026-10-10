<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Services/MahasiswaService.php';

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
        $mahasiswa = $this->repo->all();

        $this->view('mahasiswa/index', [
            'mahasiswa' => $mahasiswa
        ]);
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
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        } catch (Throwable $e) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Data mahasiswa gagal disimpan.'
            ];
        }

        $this->redirect('/BkpmWebServer/acara13/public/mahasiswa');
    }

    public function edit(int $id): void
    {
        $mahasiswa = $this->repo->find($id);

        if (!$mahasiswa) {
            http_response_code(404);
            echo 'Data mahasiswa tidak ditemukan.';
            return;
        }

        $this->view('mahasiswa/edit', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function update(int $id): void
    {
        try {
            $berhasil = $this->service->update($id, $_POST);

            $_SESSION['flash'] = [
                'type' => $berhasil ? 'success' : 'error',
                'message' => $berhasil
                    ? 'Data mahasiswa berhasil diubah.'
                    : 'Data mahasiswa gagal disimpan.'
            ];
        } catch (InvalidArgumentException $e) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        } catch (Throwable $e) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Data mahasiswa gagal disimpan.'
            ];
        }

        $this->redirect('/BkpmWebServer/acara13/public/mahasiswa');
    }

    public function destroy(int $id): void
    {
        $berhasil = $this->repo->delete($id);

    $_SESSION['flash'] = [
        'type' => $berhasil ? 'success' : 'danger',
        'message' => $berhasil
            ? 'Data mahasiswa berhasil ditambahkan.'
            : 'Data mahasiswa gagal disimpan.'
    ];

        $this->redirect('/BkpmWebServer/acara13/public/mahasiswa');
    }
}