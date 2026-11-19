<?php

class Mahasiswa
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        return $this->db
            ->query('SELECT * FROM mahasiswa ORDER BY id DESC')
            ->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM mahasiswa WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    /**
     * Memeriksa apakah NIM sudah terdaftar.
     * $exceptId digunakan saat update agar data itu sendiri
     * tidak dianggap sebagai NIM duplikat.
     */
    public function nimExists(
        string $nim,
        ?int $exceptId = null
    ): bool {
        $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
        $params = [':nim' => $nim];

        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params[':id'] = $exceptId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    // create() ini teh buat menyimpan nama berkas foto
    public function create(array $data): string
    {
        if ($this->nimExists((string) $data['nim'])) {
            throw new DomainException('NIM sudah terdaftar');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO mahasiswa (nim, nama, prodi, foto)
             VALUES (:nim, :nama, :prodi, :foto)'
        );

        $stmt->execute([
            ':nim'   => $data['nim'],
            ':nama'  => $data['nama'],
            ':prodi' => $data['prodi'],
            ':foto'  => $data['foto'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    // ($data['foto'] diisi teh gini ya controller: foto yang lila tea atauwa foto nu baru)
    public function update(int $id, array $data): void
    {
        if ($this->nimExists((string) $data['nim'], $id)) {
            throw new DomainException('NIM sudah terdaftar');
        }

        $stmt = $this->db->prepare(
            'UPDATE mahasiswa
             SET nim = :nim, nama = :nama, prodi = :prodi, foto = :foto
             WHERE id = :id'
        );

        $stmt->execute([
            ':nim'   => $data['nim'],
            ':nama'  => $data['nama'],
            ':prodi' => $data['prodi'],
            ':foto'  => $data['foto'] ?? null,
            ':id'    => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM mahasiswa WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id
        ]);
    }
}
