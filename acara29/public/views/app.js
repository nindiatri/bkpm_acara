// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

// Langkah 2: semua data dari API di-escape sebelum masuk ke innerHTML (escapeHTML ada di util.js)
function renderTabel(data) {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    tbody.innerHTML = data.map((mhs) =>
        `<tr><td>${escapeHTML(mhs.nim)}</td><td>${escapeHTML(mhs.nama)}</td>
             <td>${escapeHTML(mhs.prodi)}</td></tr>`
    ).join('');
}

// Langkah 1: indikator loading + pesan error yang jelas (bukan gagal senyap)
async function muatDataMahasiswa() {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    tbody.innerHTML = '<tr><td colspan="3">Memuat data...</td></tr>';
    try {
        // Tugas mandiri 2: otomatis dicoba ulang 1 kali bila jaringan gagal
        const response = await fetchDenganRetry(`${BASE_URL}/mahasiswa`);
        if (!response.ok) throw new Error('Server merespons dengan status ' + response.status);
        const hasil = await response.json();
        const data = hasil.data || [];

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Belum ada data</td></tr>';
            return;
        }
        renderTabel(data);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="3">Gagal memuat data: ${escapeHTML(error.message)}</td></tr>`;
        console.error('Gagal memuat data:', error);
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
