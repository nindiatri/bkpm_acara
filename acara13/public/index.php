
<?php

session_start();

/* Load file yang dibutuhkan */

require_once __DIR__ . '/../app/Core/Database.php';

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Mahasiswa.php';

require_once __DIR__ . '/../app/Repositories/MahasiswaRepository.php';
require_once __DIR__ . '/../app/Repositories/ProdiRepository.php';

require_once __DIR__ . '/../app/Services/MahasiswaService.php';

require_once __DIR__ . '/../app/Controllers/BaseController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';

require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

require_once __DIR__ . '/../routes/web.php';


/* Membuat MahasiswaController */

function createMahasiswaController(): MahasiswaController
{
    $db = Database::getInstance();

    $mahasiswaRepository = new MahasiswaRepository($db);
    $prodiRepository = new ProdiRepository($db);

    $service = new MahasiswaService(
        $mahasiswaRepository,
        $prodiRepository
    );

    return new MahasiswaController(
        $mahasiswaRepository,
        $service
    );
}


/* Mengambil URL dan method */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$base = '/BkpmWebServer/acara13/public';

if (str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

if ($uri === '') {
    $uri = '/';
}


/* Route edit mahasiswa */

if (
    $method === 'GET' &&
    preg_match('#^/mahasiswa/([0-9]+)/edit$#', $uri, $matches)
) {
    (new AuthMiddleware())->handle();

    createMahasiswaController()->edit((int) $matches[1]);
    exit;
}


/* Route update mahasiswa */

if (
    $method === 'POST' &&
    preg_match('#^/mahasiswa/([0-9]+)$#', $uri, $matches)
) {
    (new AuthMiddleware())->handle();

    createMahasiswaController()->update((int) $matches[1]);
    exit;
}


/* Route hapus mahasiswa */

if (
    $method === 'POST' &&
    preg_match('#^/mahasiswa/([0-9]+)/delete$#', $uri, $matches)
) {
    (new AuthMiddleware())->handle();

    createMahasiswaController()->destroy((int) $matches[1]);
    exit;
}


/* Mencari route */

$route = $routes[$method][$uri] ?? null;

if ($route === null) {
    http_response_code(404);
    echo '<h1>404 - Halaman Tidak Ditemukan</h1>';
    exit;
}


/* Menjalankan middleware */

if (isset($route['middleware'])) {
    foreach ($route['middleware'] as $middlewareName) {
        if ($middlewareName === 'AuthMiddleware') {
            (new AuthMiddleware())->handle();
        }
    }
}


/* Membuat controller */

$controllerName = $route['controller'];
$action = $route['action'];

if ($controllerName === 'MahasiswaController') {
    $controller = createMahasiswaController();

} elseif ($controllerName === 'AuthController') {
    $controller = new AuthController();

} elseif ($controllerName === 'HomeController') {
    $controller = new HomeController();

} else {
    http_response_code(404);
    echo '<h1>Controller Tidak Ditemukan</h1>';
    exit;
}


/* Menjalankan action */

if (!method_exists($controller, $action)) {
    http_response_code(500);
    echo "Method {$action} tidak ditemukan pada {$controllerName}.";
    exit;
}

$controller->$action();