// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

// Langkah 2: Pencegahan Cross-Site Scripting (XSS)
function escapeHTML(teks) {
    if (teks === null || teks === undefined) return '';
    const div = document.createElement('div');
    div.innerText = teks;
    return div.innerHTML;
}

// Tugas Praktik Mandiri 2: Mekanisme retry (1 kali percobaan ulang otomatis jika gagal jaringan)
async function fetchWithRetry(url, options = {}, retries = 1) {
    try {
        const response = await fetch(url, options);
        return response;
    } catch (err) {
        if (retries > 0) {
            console.warn('Gagal menghubungi server, mencoba ulang (retry)...');
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

// Langkah 1: Indikator Loading & Error Feedback
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
        tbody.innerHTML = `<tr><td colspan="3" style="text-align:center; color: #ef4444;">Gagal memuat data: ${escapeHTML(error.message)}</td></tr>`;
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
