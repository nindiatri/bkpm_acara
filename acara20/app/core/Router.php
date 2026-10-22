<?php
class Router
{
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'buku' => 'BukuController',
    ];

    public function dispatch(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        // Rute khusus autentikasi: POST /register dan POST /login

        // Rute khusus autentikasi
        $method = $_SERVER['REQUEST_METHOD'];

        if (in_array('register', $segments, true)) {
            if ($method !== 'POST') {
                header('Allow: POST');
                Response::json(405, [
                    'success' => false,
                    'message' => 'Gunakan metode POST untuk register'
                ]);
            }

            (new AuthController(Database::getConnection()))->register();
            return;
        }

        if (in_array('login', $segments, true)) {
            if ($method !== 'POST') {
                header('Allow: POST');
                Response::json(405, [
                    'success' => false,
                    'message' => 'Gunakan metode POST untuk login'
                ]);
            }

            (new AuthController(Database::getConnection()))->login();
            return;
        }


        $resource = null;
        foreach ($segments as $segment) {
            if (isset($this->map[$segment])) {
                $resource = $segment;
                break;
            }
        }

        if ($resource === null) {
            Response::json(404, [
                'success' => false,
                'message' => 'Endpoint tidak ditemukan'
            ]);
        }

        $controller = new $this->map[$resource]();
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $controller->index();
                break;
            case 'POST':
                $controller->store();
                break;
            case 'PUT':
                $controller->update($this->requireId());
                break;
            case 'DELETE':
                $controller->destroy($this->requireId());
                break;
            case 'OPTIONS':
                http_response_code(204);
                exit;
            default:
                header('Allow: GET, POST, PUT, DELETE, OPTIONS');
                Response::json(405, [
                    'success' => false,
                    'message' => 'Method tidak diizinkan'
                ]);
        }
    }

    private function requireId(): int
    {
        $id = $_GET['id'] ?? null;

        if ($id === null || !ctype_digit((string) $id)) {
            Response::json(400, [
                'success' => false,
                'message' => 'Parameter id wajib diisi dan harus berupa angka'
            ]);
        }

        return (int) $id;
    }
}
