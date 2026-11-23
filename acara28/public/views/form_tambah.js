// Alamat dasar API dihitung otomatis dari lokasi halaman
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

// Langkah 2: Escaping HTML
function escapeHTML(teks) {
    if (teks === null || teks === undefined) return '';
    const div = document.createElement('div');
    div.innerText = teks;
    return div.innerHTML;
}

// Tugas Mandiri 2: Mekanisme retry
async function fetchWithRetry(url, options = {}, retries = 1) {
    try {
        return await fetch(url, options);
    } catch (err) {
        if (retries > 0) {
            console.warn('Gagal jaringan, mencoba ulang (retry)...');
            return await fetchWithRetry(url, options, retries - 1);
        }
        throw err;
    }
}

// Langkah 3: Pola try-catch-finally dengan tombol dinonaktifkan sementara
async function simpanData(e) {
    e.preventDefault();

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;

    const nimVal = document.getElementById('nim').value.trim();
    const namaVal = document.getElementById('nama').value.trim();
    const prodiVal = document.getElementById('prodi').value.trim();
    const file = document.getElementById('foto').files[0];

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

    // Disable tombol submit & tampilkan status loading
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
        // Kembalikan tombol seperti semula setelah proses selesai
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
}

document.getElementById('form-tambah').addEventListener('submit', simpanData);
