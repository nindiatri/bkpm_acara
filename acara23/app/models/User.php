<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, nama, email, password FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Menyimpan user baru dan mengembalikan data user (tanpa password).
     *
     * @throws InvalidArgumentException jika format email tidak valid
     * @throws DomainException          jika email sudah terdaftar
     */
    public function create(string $nama, string $email, string $password): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Format email tidak valid');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (nama, email, password)
                 VALUES (:nama, :email, :password)'
            );
            $stmt->execute([
                ':nama' => $nama,
                ':email' => $email,
                ':password' => $hashedPassword,
            ]);
        } catch (PDOException $e) {
            // 1062 = duplicate entry (kolom email bersifat UNIQUE)
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                throw new DomainException('Email sudah terdaftar');
            }
            throw $e;
        }

        return [
            'id' => (int) $this->db->lastInsertId(),
            'nama' => $nama,
            'email' => $email,
        ];
    }
}
