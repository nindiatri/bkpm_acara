// Alamat dasar API dihitung otomatis dari lokasi halaman
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

async function simpanData(e) {
    e.preventDefault();

    const nimVal = document.getElementById('nim').value.trim();
    const namaVal = document.getElementById('nama').value.trim();
    const prodiVal = document.getElementById('prodi').value.trim();
    const file = document.getElementById('foto').files[0];

    // Tugas 2: validasi di View sebelum request dikirim
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

    const response = await fetch(`${BASE_URL}/mahasiswa`, {
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

    // Tugas 1: notifikasi visual
    if (hasil.success) {
        simpanNotifBerikutnya(formatPesan(response.status, hasil), 'sukses');
        window.location.href = 'dashboard.html';
    } else {
        tampilNotif(formatPesan(response.status, hasil), 'error');
    }
}

document.getElementById('form-tambah').addEventListener('submit', simpanData);