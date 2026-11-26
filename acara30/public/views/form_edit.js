// Alamat dasar API otomatis: mendukung akses dengan /public/views maupun langsung /views
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views(\/[^/]*)?$/, '');

// id diambil dari query parameter: form_edit.html?id=16
const id = new URLSearchParams(window.location.search).get('id');

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
            console.warn('[Network Alert] Gangguan jaringan sementara. Mencoba ulang (retry 1x)...');
            return await fetchWithRetry(url, options, retries - 1);
        }
        throw err;
    }
}

// Mengisi form dengan data lama lewat GET /mahasiswa?id=...
async function muatDataUntukEdit(id) {
    try {
        const res = await fetchWithRetry(`${BASE_URL}/mahasiswa?id=${id}`);
        const hasil = await res.json();

        if (!hasil.success) {
            alert(`[${res.status}] ${hasil.message || 'Data tidak ditemukan'}`);
            window.location.href = 'dashboard.html';
            return;
        }

        document.getElementById('nim').value = hasil.data.nim || '';
        document.getElementById('nama').value = hasil.data.nama || '';
        document.getElementById('prodi').value = hasil.data.prodi || '';

        if (hasil.data.foto) {
            const img = document.getElementById('foto-lama');
            img.src = `${BASE_URL}/uploads/${encodeURIComponent(hasil.data.foto)}`;
            img.hidden = false;
            document.getElementById('tanpa-foto').hidden = true;
        }
    } catch (err) {
        alert('Gagal memuat data mahasiswa: ' + err.message);
        window.location.href = 'dashboard.html';
    }
}

// Langkah 3 — Menambahkan Umpan Balik pada Form (try-catch-finally)
async function simpanPerubahan(id, formEl) {
    const submitBtn = formEl.querySelector('button[type="submit"]');
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

    // Langkah 3: Tombol submit dinonaktifkan sementara (loading state)
    submitBtn.disabled = true;
    submitBtn.textContent = 'Memperbarui...';

    try {
        const response = await fetchWithRetry(`${BASE_URL}/mahasiswa?id=${id}&_method=PUT`, {
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
    } catch (err) {
        tampilNotif(`Terjadi kesalahan jaringan: ${escapeHTML(err.message)}`, 'error');
        console.error('Error saat simpan perubahan:', err);
    } finally {
        // Langkah 3: Kembalikan tombol seperti semula di blok finally
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (!id) {
        alert('ID mahasiswa tidak disertakan.');
        window.location.href = 'dashboard.html';
        return;
    }

    muatDataUntukEdit(id);

    const formEdit = document.getElementById('form-edit');
    formEdit.addEventListener('submit', (e) => {
        e.preventDefault();
        simpanPerubahan(id, formEdit);
    });
});
