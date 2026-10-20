<?php

header('Content-Type: application/json');

//Fungsi untuk mengirim response JSON beserta HTTP Status Code
function kirimResponse($status, $success, $message, $data = null)
{
    http_response_code($status);

    $response = [
        'success' => $success,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response);
    exit;
}

//Fungsi untuk mencari mahasiswa berdasarkan id
function cariMahasiswa($pdo, $id)
{
    if (!ctype_digit((string) $id)) {
        return false;
    }

    $stmt = $pdo->prepare(
        'SELECT id, nim, nama, email 
         FROM mahasiswa 
         WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//Fungsi untuk membaca body JSON dari request
function ambilInputJson()
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        kirimResponse(400, false, 'Data JSON tidak valid');
    }

    return $input;
}

//Fungsi untuk memvalidasi data mahasiswa (nim, nama, email)
function validasiMahasiswa($input)
{
    foreach (['nim', 'nama', 'email'] as $field) {
        if (!isset($input[$field]) || !is_string($input[$field])) {
            kirimResponse(400, false, 'Data JSON tidak lengkap');
        }
    }

    if (trim($input['nim']) === '') {
        kirimResponse(400, false, 'NIM tidak boleh kosong');
    }

    if (trim($input['nama']) === '') {
        kirimResponse(400, false, 'Nama tidak boleh kosong');
    }

    if (!filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL)) {
        kirimResponse(400, false, 'Format email tidak valid');
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;

//id juga bisa dikirim lewat URL, contoh: mahasiswa.php/1
if ($id === null && !empty($_SERVER['PATH_INFO'])) {
    $id = trim($_SERVER['PATH_INFO'], '/');

    if ($id === '') {
        $id = null;
    }
}

try {
    //Koneksi PDO
    $pdo = new PDO(
        'mysql:host=localhost;dbname=si_akademik;charset=utf8mb4',
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($method === 'GET' && $id === null) {
        //GET /api/mahasiswa -> ambil seluruh data mahasiswa
        $stmt = $pdo->query(
            'SELECT id, nim, nama, email 
             FROM mahasiswa'
        );
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        kirimResponse(200, true, 'Data berhasil diambil', $data);

    } elseif ($method === 'GET') {
        //GET /api/mahasiswa/1 -> ambil data berdasarkan id
        $data = cariMahasiswa($pdo, $id);

        if (!$data) {
            kirimResponse(404, false, 'Data tidak ditemukan');
        }

        kirimResponse(200, true, 'Data berhasil diambil', $data);

    } elseif ($method === 'POST' && $id === null) {
        //POST /api/mahasiswa -> tambah data mahasiswa
        $input = ambilInputJson();
        validasiMahasiswa($input);

        $stmt = $pdo->prepare(
            'INSERT INTO mahasiswa (nim, nama, email, prodi) 
             VALUES (:nim, :nama, :email, :prodi)'
        );
        $stmt->execute([
            'nim'   => trim($input['nim']),
            'nama'  => trim($input['nama']),
            'email' => trim($input['email']),
            'prodi' => 'Teknik Informatika',
        ]);

        $data = cariMahasiswa($pdo, $pdo->lastInsertId());

        kirimResponse(201, true, 'Data berhasil ditambahkan', $data);

    } elseif ($method === 'PUT' && $id !== null) {
        //PUT /api/mahasiswa/1 -> ubah data mahasiswa
        if (!cariMahasiswa($pdo, $id)) {
            kirimResponse(404, false, 'Data tidak ditemukan');
        }

        $input = ambilInputJson();
        validasiMahasiswa($input);

        $stmt = $pdo->prepare(
            'UPDATE mahasiswa 
             SET nim = :nim, nama = :nama, email = :email 
             WHERE id = :id'
        );
        $stmt->execute([
            'nim'   => trim($input['nim']),
            'nama'  => trim($input['nama']),
            'email' => trim($input['email']),
            'id'    => $id,
        ]);

        $data = cariMahasiswa($pdo, $id);

        kirimResponse(200, true, 'Data berhasil diubah', $data);

    } elseif ($method === 'DELETE' && $id !== null) {
        //DELETE /api/mahasiswa/1 -> hapus data mahasiswa
        if (!cariMahasiswa($pdo, $id)) {
            kirimResponse(404, false, 'Data tidak ditemukan');
        }

        $stmt = $pdo->prepare('DELETE FROM mahasiswa WHERE id = :id');
        $stmt->execute(['id' => $id]);

        kirimResponse(200, true, 'Data berhasil dihapus');

    } else {
        kirimResponse(400, false, 'Request tidak valid');
    }
} catch (PDOException $e) {
    kirimResponse(500, false, 'Terjadi error pada server');
}
