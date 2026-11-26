// Alamat dasar API otomatis: mendukung akses dengan /public/views maupun langsung /views
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views(\/[^/]*)?$/, '');

// Langkah 2 & Tugas Mandiri 1 — Fungsi Escaping Output (XSS)
function escapeHTML(teks) {
    if (teks === null || teks === undefined) return '';
    const div = document.createElement('div');
    div.innerText = teks;
    return div.innerHTML;
}

// Tugas Praktik Mandiri 2 — Mekanisme Retry Otomatis 1 Kali
async function fetchWithRetry(url, options = {}, retries = 1) {
    try {
        return await fetch(url, options);
    } catch (err) {
        if (retries > 0) {
            console.warn('[Network Alert] Gagal jaringan sementara. Melakukan percobaan ulang (retry 1x)...');
            return await fetchWithRetry(url, options, retries - 1);
        }
        throw err;
    }
}

// Langkah 3 Menambahkan Umpan Balik pada Form (try-catch-finally)
async function simpanData(e) {
    e.preventDefault();

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;

    const nimVal = document.getElementById('nim').value.trim();
    const namaVal = document.getElementById('nama').value.trim();
    const prodiVal = document.getElementById('prodi').value.trim();
    const file = document.getElementById('foto').files[0];

    // Validasi sebelum request
    const errors = validasiMahasiswa(nimVal, namaVal, prodiVal, file);
    if (errors.length > 0) {
        tampilNotif(errors.join(' | '), 'error');
        return;
    }

    const formData = new FormData();
    formData.append('nim', nimVal);
    formData.append('nama', namaVal);
    formData.append('prodi', prodiVal);
    if (file) formData.append('foto', file);

    // Langkah 3 Menonaktifkan tombol submit sementara agar mencegah double-click
    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';

    try {
        const response = await fetchWithRetry(`${BASE_URL}/mahasiswa`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
            body: formData,
        });

        const hasil = await response.json();

        if (response.status === 401) {
            localStorage.clear();
            alert('Sesi berakhir, silakan login kembali.');
            window.location.href = 'login.html';
            return;
        }

        // Tampilkan feedback sukses / gagal
        if (hasil.success) {
            simpanNotifBerikutnya(formatPesan(response.status, hasil), 'sukses');
            window.location.href = 'dashboard.html';
        } else {
            tampilNotif(formatPesan(response.status, hasil), 'error');
        }
    } catch (error) {
        tampilNotif(`Terjadi kesalahan jaringan: ${escapeHTML(error.message)}`, 'error');
        console.error('Error saat simpan data:', error);
    } finally {
        // Langkah 3: Kembalikan tombol seperti semula di blok finally
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
}

document.getElementById('form-tambah').addEventListener('submit', simpanData);
