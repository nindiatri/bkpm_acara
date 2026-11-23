// Alamat dasar API dihitung otomatis dari lokasi halaman
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

// Acara 29 Langkah 3: try-catch-finally, tombol nonaktif selama pengiriman, pesan sukses/gagal jelas
async function simpanData(e) {
    e.preventDefault();
    const tombol = e.currentTarget.querySelector('button[type="submit"]');

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

    setTombolProses(tombol, true, 'Menyimpan...');
    try {
        const response = await fetchDenganRetry(`${BASE_URL}/mahasiswa`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
            body: formData,
        });
        const hasil = await bacaJson(response);

        if (response.status === 401) {
            localStorage.clear();
            simpanNotifBerikutnya('Sesi berakhir, silakan login kembali.', 'error');
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
    } catch (error) {
        console.error('Gagal menyimpan data:', error);
        tampilNotif(pesanKesalahan(error), 'error');
    } finally {
        setTombolProses(tombol, false);
    }
}

document.getElementById('form-tambah').addEventListener('submit', simpanData);
