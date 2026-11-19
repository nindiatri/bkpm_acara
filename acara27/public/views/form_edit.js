// Alamat dasar API dihitung otomatis dari lokasi halaman
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

// id diambil dari alamat halaman: form_edit.html?id=16
const id = new URLSearchParams(window.location.search).get('id');

// Mengisi form dengan data lama lewat GET /mahasiswa?id=...
async function muatDataUntukEdit(id) {
    const res = await fetch(`${BASE_URL}/mahasiswa?id=${id}`);
    const hasil = await res.json(); // ditangani MahasiswaController::index()

    if (!hasil.success) {
        alert(`[${res.status}] ${hasil.message || 'Data tidak ditemukan'}`);
        window.location.href = 'dashboard.html';
        return;
    }

    document.getElementById('nim').value = hasil.data.nim;
    document.getElementById('nama').value = hasil.data.nama;
    document.getElementById('prodi').value = hasil.data.prodi;

    // Tampilkan foto lama bila ada
    if (hasil.data.foto) {
        const img = document.getElementById('foto-lama');
        img.src = `${BASE_URL}/uploads/${encodeURIComponent(hasil.data.foto)}`;
        img.hidden = false;
        document.getElementById('tanpa-foto').hidden = true;
    }
}

// Menyimpan perubahan: POST + _method=PUT agar multipart terbaca PHP,
// lalu diteruskan Router ke MahasiswaController::update($id)
async function simpanPerubahan(id) {
    const nimVal = document.getElementById('nim').value.trim();
    const namaVal = document.getElementById('nama').value.trim();
    const prodiVal = document.getElementById('prodi').value.trim();
    const file = document.getElementById('foto').files[0];

    // Tugas 2: validasi di View
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

    const response = await fetch(`${BASE_URL}/mahasiswa?id=${id}&_method=PUT`, {
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

if (!id) {
    alert('ID mahasiswa tidak ditemukan.');
    window.location.href = 'dashboard.html';
} else {
    muatDataUntukEdit(id);
    document.getElementById('form-edit').addEventListener('submit', (e) => {
        e.preventDefault();
        simpanPerubahan(id);
    });
}