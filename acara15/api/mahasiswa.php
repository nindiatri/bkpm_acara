<?php

header('Content-Type: application/json');

//Koneksi PDO
$pdo = new PDO(
    'mysql:host=localhost;dbname=si_akademik;charset=utf8mb4',
    'root',
    ''
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Tudas POST
    $input = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare(
        'INSERT INTO mahasiswa (nim, nama, email, prodi) 
         VALUES (:nim, :nama, :email, :prodi)'
    );
    $stmt->execute([
        'nim'   => $input['nim'],
        'nama'  => $input['nama'],
        'email' => $input['email'],
        'prodi' => 'Teknik Informatika',
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil ditambahkan',
        'data'    => $input
    ]);
} elseif (isset($_GET['id'])) {
    //Tugas GET?id=1
    $stmt = $pdo->prepare(
        'SELECT id, nim, nama, email 
         FROM mahasiswa 
         WHERE id = :id'
    );
    $stmt->execute(['id' => $_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data'    => $data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }
} else {
        //ambil data mahasiswa pake SELECT
        $stmt = $pdo->query(
        'SELECT id, nim, nama, email 
         FROM mahasiswa'
    );
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil diambil',
        'data'    => $data
    ]);
}
