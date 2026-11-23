<?php
// =====================================================================
//  HALAMAN PEMERIKSA SEMENTARA  (HAPUS FILE INI SETELAH SELESAI DICEK!)
//  Buka:  https://NAMA-DOMAIN-ANDA/cek.php
// =====================================================================
header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/../app/config/Database.php';

$hasil = [];
function tambah(array &$h, string $nama, bool $ok, string $info, string $solusi = ''): void
{
    $h[] = compact('nama', 'ok', 'info', 'solusi');
}

// 1. Versi PHP
tambah($GLOBALS['hasil'], 'Versi PHP', version_compare(PHP_VERSION, '8.1.0', '>='),
    'PHP ' . PHP_VERSION, 'Aplikasi butuh PHP 8.1 ke atas.');

// 2. Ekstensi
foreach (['pdo_mysql' => 'Koneksi database (PDO MySQL)', 'json' => 'JSON', 'fileinfo' => null] as $ext => $label) {
    if ($label === null) { continue; }
    tambah($GLOBALS['hasil'], 'Ekstensi ' . $ext, extension_loaded($ext), $label, 'Hubungi dukungan hosting.');
}
tambah($GLOBALS['hasil'], 'Pembaca tipe gambar', function_exists('exif_imagetype') || function_exists('getimagesize'),
    'exif_imagetype / getimagesize tersedia', 'Dibutuhkan untuk validasi upload foto.');

// 3. Folder uploads bisa ditulisi
$up = __DIR__ . '/uploads/';
$tes = $up . 'tes_' . bin2hex(random_bytes(4)) . '.txt';
$bisaTulis = is_dir($up) && @file_put_contents($tes, 'ok') !== false;
if ($bisaTulis) { @unlink($tes); }
tambah($GLOBALS['hasil'], 'Folder uploads bisa ditulisi', $bisaTulis,
    $bisaTulis ? 'Berhasil menulis & menghapus berkas uji' : 'Gagal menulis ke public/uploads',
    'Pastikan folder public/uploads ada. Jika perlu atur permission ke 755 lewat FileZilla.');

// 4. Pengaturan database sudah diisi?
$masihContoh = false;
foreach ([DB_HOST, DB_NAME, DB_USER, DB_PASS] as $v) {
    if (strpos($v, 'ISI_') === 0) { $masihContoh = true; }
}
tambah($GLOBALS['hasil'], 'Database.php sudah diisi', !$masihContoh,
    $masihContoh ? 'Masih ada tulisan ISI_... di app/config/Database.php' : 'Terisi',
    'Edit app/config/Database.php, isi 4 nilai dari panel MySQL Databases, lalu upload ulang.');

// 5. Koneksi database + tabel
$pdo = null;
if (!$masihContoh) {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 8]);
        tambah($GLOBALS['hasil'], 'Koneksi ke database', true, 'Berhasil terhubung');
    } catch (Throwable $e) {
        tambah($GLOBALS['hasil'], 'Koneksi ke database', false, 'Gagal: ' . $e->getMessage(),
            'Periksa lagi host, nama database, username, dan password (salin-tempel dari panel). Host BUKAN "localhost".');
    }
}
if ($pdo) {
    foreach (['users', 'mahasiswa', 'buku'] as $tb) {
        try {
            $n = (int) $pdo->query("SELECT COUNT(*) FROM `$tb`")->fetchColumn();
            tambah($GLOBALS['hasil'], "Tabel $tb", true, "Ada, berisi $n baris");
        } catch (Throwable $e) {
            tambah($GLOBALS['hasil'], "Tabel $tb", false, 'Tidak ditemukan', 'Import file db_workshop_infinityfree.sql lewat phpMyAdmin.');
        }
    }
    try {
        $pdo->query('SELECT foto FROM mahasiswa LIMIT 1');
        tambah($GLOBALS['hasil'], 'Kolom foto di tabel mahasiswa', true, 'Ada');
    } catch (Throwable $e) {
        tambah($GLOBALS['hasil'], 'Kolom foto di tabel mahasiswa', false, 'Tidak ada', 'Import ulang db_workshop_infinityfree.sql.');
    }
}

// 6. Kunci rahasia JWT bukan nilai bawaan modul
$auth = (string) @file_get_contents(__DIR__ . '/../app/core/Auth.php');
$aman = strpos($auth, 'kunci_rahasia_workshop_2026') === false;
tambah($GLOBALS['hasil'], 'Kunci rahasia token (SECRET)', $aman,
    $aman ? 'Sudah memakai kunci acak' : 'Masih memakai kunci bawaan modul', 'Ganti nilai SECRET di app/core/Auth.php.');

$semuaOk = !in_array(false, array_column($hasil, 'ok'), true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Pemeriksaan Hosting</title>
<style>
body{font-family:Arial,sans-serif;max-width:760px;margin:24px auto;padding:0 12px;color:#222}
table{border-collapse:collapse;width:100%}
td,th{border:1px solid #ccc;padding:8px;text-align:left;vertical-align:top}
.ok{color:#0a7a2f;font-weight:bold}.gagal{color:#b00020;font-weight:bold}
.kotak{padding:12px;border-radius:6px;margin:16px 0}
.hijau{background:#e6f6ea}.merah{background:#fdeaea}.kuning{background:#fff6dd}
small{color:#555}
</style>
</head>
<body>
<h1>Pemeriksaan Hosting</h1>
<div class="kotak <?= $semuaOk ? 'hijau' : 'merah' ?>">
<?= $semuaOk ? 'SEMUA PEMERIKSAAN LULUS. Aplikasi siap dipakai.' : 'Ada yang belum beres. Lihat kolom "Cara memperbaiki" di bawah.' ?>
</div>
<table>
<tr><th>Pemeriksaan</th><th>Status</th><th>Keterangan</th><th>Cara memperbaiki</th></tr>
<?php foreach ($hasil as $r): ?>
<tr>
<td><?= htmlspecialchars($r['nama']) ?></td>
<td class="<?= $r['ok'] ? 'ok' : 'gagal' ?>"><?= $r['ok'] ? 'OK' : 'GAGAL' ?></td>
<td><?= htmlspecialchars($r['info']) ?></td>
<td><small><?= $r['ok'] ? '-' : htmlspecialchars($r['solusi']) ?></small></td>
</tr>
<?php endforeach; ?>
</table>
<div class="kotak kuning">
<b>PENTING:</b> setelah selesai, <b>hapus file <code>cek.php</code></b> dari folder <code>htdocs/public</code> lewat FileZilla.
</div>
<p><a href="/views/login.html">Ke halaman login</a></p>
</body>
</html>
