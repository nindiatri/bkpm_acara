<?php

require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../Repositories/ProdiRepository.php';

class MahasiswaService
{
    private MahasiswaRepository $mahasiswaRepository;
    private ProdiRepository $prodiRepository;

    public function __construct(
        MahasiswaRepository $mahasiswaRepository,
        ProdiRepository $prodiRepository
    ) {
        $this->mahasiswaRepository = $mahasiswaRepository;
        $this->prodiRepository = $prodiRepository;
    }

    public function create(array $data): bool
    {
        $nim = trim($data['nim'] ?? '');
        $nama = trim($data['nama'] ?? '');
        $email = trim($data['email'] ?? '');
        $prodi = trim($data['prodi'] ?? '');
        $status = $data['status'] ?? 'aktif';
        $dosenId = !empty($data['dosen_id'])
            ? (int) $data['dosen_id']
            : null;

        if ($nim === '') {
            throw new InvalidArgumentException('NIM wajib diisi.');
        }

        if ($nama === '') {
            throw new InvalidArgumentException('Nama wajib diisi.');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Format email tidak valid.');
        }

        if ($this->mahasiswaRepository->findByNim($nim)) {
            throw new InvalidArgumentException('NIM sudah terdaftar.');
        }

        $prodi = $this->validateProdi($prodi);

        $mahasiswa = new Mahasiswa(
            $nim,
            $nama,
            $prodi,
            $status,
            $dosenId,
            $email
        );

        return $this->mahasiswaRepository->create($mahasiswa);
    }

    public function update(int $id, array $data): bool
    {
        $nim = trim($data['nim'] ?? '');
        $nama = trim($data['nama'] ?? '');
        $email = trim($data['email'] ?? '');
        $prodi = trim($data['prodi'] ?? '');
        $status = $data['status'] ?? 'aktif';
        $dosenId = !empty($data['dosen_id'])
            ? (int) $data['dosen_id']
            : null;

        if ($nim === '') {
            throw new InvalidArgumentException('NIM wajib diisi.');
        }

        if ($nama === '') {
            throw new InvalidArgumentException('Nama wajib diisi.');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Format email tidak valid.');
        }

        $existing = $this->mahasiswaRepository->findByNim($nim);

        if ($existing && (int) $existing['id'] !== $id) {
            throw new InvalidArgumentException('NIM sudah terdaftar.');
        }

        $prodi = $this->validateProdi($prodi);

        $mahasiswa = new Mahasiswa(
            $nim,
            $nama,
            $prodi,
            $status,
            $dosenId,
            $email
        );

        return $this->mahasiswaRepository->update($id, $mahasiswa);
    }

    private function validateProdi(string $prodi): string
    {
        if ($prodi === '') {
            throw new InvalidArgumentException(
                'Program studi wajib dipilih.'
            );
        }

        foreach ($this->prodiRepository->all() as $item) {
            if (
                (string) $item['id'] === $prodi ||
                strcasecmp((string) $item['nama'], $prodi) === 0
            ) {
                return (string) $item['nama'];
            }
        }

        throw new InvalidArgumentException(
            'Program studi tidak tersedia.'
        );
    }
}