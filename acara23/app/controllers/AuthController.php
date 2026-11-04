<?php

class AuthController
{
    private User $user;

    public function __construct(PDO $db)
    {
        $this->user = new User($db);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Allow: POST');
            Response::json(405, [
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!is_array($input)) {
            Response::json(400, [
                'success' => false,
                'message' => 'Body request harus berupa JSON yang valid'
            ]);
        }

        $nama = trim((string) ($input['nama'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        $errors = [];
        if ($nama === '') {
            $errors['nama'] = 'Nama wajib diisi';
        }
        if ($email === '') {
            $errors['email'] = 'Email wajib diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid';
        }
        if ($password === '') {
            $errors['password'] = 'Password wajib diisi';
        }

        if ($errors !== []) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => $errors
            ]);
        }

        try {
            $data = $this->user->create($nama, $email, $password);
        } catch (InvalidArgumentException $e) {
            Response::json(422, [
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => ['email' => $e->getMessage()]
            ]);
        } catch (DomainException $e) {
            Response::json(409, [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => ['email' => $e->getMessage()]
            ]);
        }

        Response::json(201, [
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data' => $data
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Allow: POST');
            Response::json(405, [
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            Response::json(400, [
                'success' => false,
                'message' => 'Body request harus berupa JSON yang valid'
            ]);
        }

        // Registrasi Acara 19 menggunakan email, bukan username.
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            Response::json(422, [
                'success' => false,
                'message' => 'Email dan password wajib diisi'
            ]);
        }

        $user = $this->user->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Response::json(401, [
                'success' => false,
                'message' => 'Email atau password salah'
            ]);
        }

        $token = Auth::generateToken([
            'id' => (int) $user['id'],
            'email' => $user['email']
        ]);

        Response::json(200, [
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'user' => [
                    'id' => (int) $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email']
                ]
            ]
        ]);
    }
}
