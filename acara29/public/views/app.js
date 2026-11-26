// Alamat dasar API otomatis: mendukung akses dengan /public/views maupun langsung /views
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views(\/[^/]*)?$/, '');

// Langkah 2 — Menambahkan Fungsi Escaping Output (XSS)
function escapeHTML(teks) {
    if (teks === null || teks === undefined) return '';
    const div = document.createElement('div');
    div.innerText = teks;
    return div.innerHTML;
}

// Tugas Praktik Mandiri 2 — Mekanisme Retry Otomatis 1 Kali
async function fetchWithRetry(url, options = {}, retries = 1) {
    try {
        const response = await fetch(url, options);
        return response;
    } catch (err) {
        if (retries > 0) {
            console.warn('[Network Alert] Terjadi kegagalan jaringan sementara. Melakukan percobaan ulang (retry 1x)...');
            return await fetchWithRetry(url, options, retries - 1);
        }
        throw err;
    }
}

function renderTabel(data) {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Belum ada data</td></tr>';
        return;
    }

    tbody.innerHTML = data.map((mhs) =>
        `<tr>
            <td>${escapeHTML(mhs.nim)}</td>
            <td>${escapeHTML(mhs.nama)}</td>
            <td>${escapeHTML(mhs.prodi)}</td>
        </tr>`
    ).join('');
}

// Langkah 1 — Menambahkan Indikator Loading pada app.js
async function muatDataMahasiswa() {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Memuat data...</td></tr>';

    try {
        const response = await fetchWithRetry(`${BASE_URL}/mahasiswa`);
        if (!response.ok) {
            throw new Error('Server merespons dengan status ' + response.status);
        }
        const hasil = await response.json();
        renderTabel(hasil.data);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="3" style="text-align:center; color:#ef4444;">Gagal memuat data: ${escapeHTML(error.message)}</td></tr>`;
        console.error('Gagal memuat data:', error);
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
