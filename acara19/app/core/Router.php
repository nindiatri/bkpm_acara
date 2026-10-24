<?php
class Router
{
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'buku' => 'BukuController',
        'register' => 'AuthController',
    ];


public function dispatch(): void
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $segments = array_values(array_filter(explode('/', trim($path, '/'))));

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
        return;
    }

    $method = $_SERVER['REQUEST_METHOD'];

    // Khusus endpoint registrasi
    if ($resource === 'register') {
        $controller = new AuthController();

        if ($method === 'POST') {
            $controller->register();
            return;
        }

        header('Allow: POST, OPTIONS');

        if ($method === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        Response::json(405, [
            'success' => false,
            'message' => 'Method tidak diizinkan'
        ]);
        return;
    }

    // Endpoint Mahasiswa dan Buku tetap menggunakan alur lama
    $controller = new $this->map[$resource]();

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
            return;
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
