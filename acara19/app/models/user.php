<?php

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($nama, $email, $password)
    {
        // Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Format email tidak valid'
            ];
        }

        // Hash password sebelum disimpan
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {
            $sql = "INSERT INTO users (nama, email, password)
                    VALUES (:nama, :email, :password)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':nama' => $nama,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            return [
                'success' => true,
                'message' => 'Registrasi berhasil'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Registrasi gagal. Email mungkin sudah digunakan.'
            ];
        }
    }
}
