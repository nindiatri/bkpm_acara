<?php
class Router
{
    // Peta resource pada URL => nama class Controller
    private array $map = [
        'mahasiswa' => 'MahasiswaController',
        'buku'      => 'BukuController',
    ];

    public function dispatch(): void
    {
        $path     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        // Cari segmen pertama yang cocok dengan peta resource.
        // Contoh: /api/mahasiswa, /mahasiswa, /index.php/mahasiswa
        $resource = null;
        foreach ($segments as $segment) {
            if (isset($this->map[$segment])) {
                $resource = $segment;
                break;
            }
        }

        if ($resource === null) {
            $this->json(404, ['success' => false, 'message' => 'Endpoint tidak ditemukan']);
        }

        $controller = new $this->map[$resource]();
        $method     = $_SERVER['REQUEST_METHOD'];

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
                // Untuk preflight CORS (header CORS diatur terpisah)
                http_response_code(204);
                break;

            default:
                header('Allow: GET, POST, PUT, DELETE, OPTIONS');
                $this->json(405, ['success' => false, 'message' => 'Method tidak diizinkan']);
        }
    }

    private function requireId(): int
    {
        $id = $_GET['id'] ?? null;

        if ($id === null || !ctype_digit((string) $id)) {
            $this->json(400, ['success' => false, 'message' => 'Parameter id wajib diisi dan harus berupa angka']);
        }
        return (int) $id;
    }

    private function json(int $status, array $payload): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
