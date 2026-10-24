
<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $user;

    public function __construct()
    {
        $db = Database::getConnection();
        $this->user = new User($db);
    }

    public function register()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            return;
        }

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($input) ||
            empty($input['nama']) ||
            empty($input['email']) ||
            empty($input['password'])
        ) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Nama, email, dan password wajib diisi'
            ]);
            return;
        }

        $result = $this->user->create(
            trim($input['nama']),
            trim($input['email']),
            $input['password']
        );

        if (!$result['success']) {
            http_response_code(400);
        } else {
            http_response_code(201);
        }

        echo json_encode($result);
    }
}